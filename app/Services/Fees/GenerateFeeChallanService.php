<?php

namespace App\Services\Fees;

use App\Models\ArrearChallanDetails;
use App\Models\CampusFeesMaster;
use App\Models\ChallanPartialPayment;
use App\Models\ChallanTransactions;
use App\Models\FeeLog;
use App\Models\GenerateFeeChallan;
use App\Models\OptionalFeeMaster;
use App\Models\Student;
use App\Models\StudentFeeDiscount;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class GenerateFeeChallanService
{

    public function index($request): array
    {
        $data['classAndSections'] = classAndSections();
        $challans = GenerateFeeChallan::where('tenant_id', tenant('id'))
            ->with('ChallanTransactions', 'StudentRel','ClassRel','SectionRel')
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;

            $challans->where(function ($q) use ($search) {
                $q->where('challan_no', 'like', "%{$search}%")
                ->orWhereHas('StudentRel', function ($sub) use ($search) {
                $sub->where('FirstName', 'like', "%{$search}%")
                    ->orWhere('LastName', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(FirstName, ' ', LastName) LIKE ?", ["%{$search}%"]);
                })
                ->orWhereHas('ClassRel', function ($sub) use ($search) {
                    $sub->where('ClassName', 'like', "%{$search}%");
                })
                ->orWhereHas('SectionRel', function ($sub) use ($search) {
                    $sub->where('SectionName', 'like', "%{$search}%");
                });
            });
        }

        $data['challansList'] =  $challans->paginate(25)->withQueryString();
        return $data;
    }

    public function submit($request)
    {
        $per_day_fine = getSiteMeta('Fine_Late_Fee');
        $currentSession = fetchCurrentSession();
        $requestedMonth = Carbon::createFromFormat('Y-m', $request->ChallanMonth)->startOfMonth();
        foreach ($request->StudentId as $key => $value) {
            $date = $request->ChallanMonth . "-01";

            // If this month is already covered by a combined challan (Fee Cycle), block single-month generation.
            $existingCombined = GenerateFeeChallan::where('tenant_id', tenant('id'))
                ->where('StudentId', $value['id'])
                ->whereNotNull('Note')
                ->where('Note', 'like', '%Fee Cycle:%')
                ->orderBy('id', 'desc')
                ->get(['id', 'Note']);

            foreach ($existingCombined as $existingRow) {
                $range = $this->parseFeeCycleRange($existingRow->Note);
                if (!$range) {
                    continue;
                }
                [$existingStart, $existingEnd] = $range;
                if ($existingStart->lte($requestedMonth) && $requestedMonth->lte($existingEnd)) {
                    $studentData = Student::where('tenant_id', tenant('id'))
                        ->where('IsDisable', 0)
                        ->where('id', $value['id'])
                        ->first(['FirstName']);

                    return throw ValidationException::withMessages([
                        'StudentId' => 'Challan already created for this student (Fee Cycle covers ' . $requestedMonth->format('M-Y') . '): ' . ($studentData->FirstName ?? ''),
                    ]);
                }
            }

            $generateFeeChallanExist = GenerateFeeChallan::where('tenant_id', tenant('id'))
                ->where('StudentId', $value['id'])
                ->where('ChallanMonth', $date)
                ->first();
            if ($generateFeeChallanExist) {
                $studentData = Student::where('tenant_id', tenant('id'))->where('IsDisable', 0)->where('id', $generateFeeChallanExist->StudentId)->first('FirstName');
                return throw ValidationException::withMessages([
                    'StudentId' => 'Challan already created for this student: ' . $studentData->FirstName,
                ]);
            }
            $challanNo = generateChallanNo();
             
            $previousChallan = GenerateFeeChallan::where('tenant_id', tenant('id'))
                ->where('StudentId', $value['id'])
                ->orderBy('id', 'desc')
                ->first();
            
            $generateChallan = new GenerateFeeChallan();
            $generateChallan->tenant_id = tenant('id');
            $generateChallan->challan_no = $challanNo;
            $generateChallan->per_day_fine = $per_day_fine;
            $generateChallan->IsActive = 1;
            $generateChallan->StudentId = $value['id'];
            $generateChallan->ChallanMonth = $request->ChallanMonth . '-01';
            $generateChallan->DueDate = $request->DueDate;
            $generateChallan->SessionId = $currentSession->id;
            $generateChallan->ClassId = $request->ClassId;
            $generateChallan->SectionId = $request->SectionId;
            $generateChallan->ExpiryDate = $request->ExpiryDate;
            $generateChallan->Status = 'Unpaid';
            $generateChallan->IsPartialPayment = 0;
            $generateChallan->FineAmount = 0;
            $generateChallan->CreatedBy = auth()->user()->id;
            if($generateChallan->save()){
                userActivityLogs('Generate Fee Challan and By User ID: '.auth()->user()->id.'', FeeLog::class);
                $studentData = Student::select('id','tenant_id','FirstName','LastName')->with('userFCMToken')->where('tenant_id', tenant('id'))->where('id', $value['id'])->first();
                $title = "New Challan Generated";
                $body = "New challan generated for " . $studentData->FirstName . " " . $studentData->LastName . " successfully.";
                try {
                sendTestNotification($request, $title, $body, $studentData);
                } catch (\Exception $e) {
                    \Log::error('FCM Error: ' . $e->getMessage());
                }
            }

            if ($previousChallan) {
                $this->checkArearsAndFines($previousChallan, $generateChallan);
            }

            $campusMasters = CampusFeesMaster::select('id', 'tenant_id', 'FeesTypeNId', 'Amount', 'ClassId')
                ->Class($request->ClassId)
                ->Tenant()
                ->where('SessionId', $currentSession->id)
                ->whereHas('FeeTypeRel')
                ->with('FeeTypeRel')
                ->get()->toArray();
            // dd($campusMasters);
            foreach ($campusMasters as $master) {
                if ($master['fee_type_rel']['FeeCycle'] === 'Once') {
                    $this->isCampusMasterOnce($request, $generateChallan, $currentSession, $master, $value);
                }
                if ($master['fee_type_rel']['FeeCycle'] === 'Annually') {
                    $this->isCampusMasterAnnually($request, $generateChallan, $currentSession, $master, $value);
                }
                if ($master['fee_type_rel']['FeeCycle'] === 'Monthly') {
                    $this->isCampusMasterMonthly($generateChallan, $currentSession, $master, $value);
                }
            }
        }
    }

    // public function submitMultiMonth($request)
    // {
    //     $per_day_fine = getSiteMeta('Fine_Late_Fee');
    //     $currentSession = fetchCurrentSession();

    //     $months = collect($request->ChallanMonths ?? [])
    //         ->filter()
    //         ->map(function ($month) {
    //             return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    //         })
    //         ->sort()
    //         ->values();
            

    //     foreach ($months as $month) {
    //         $monthRequest = clone $request;
    //         $monthString = $month->format('Y-m');
    //         [$dueDate, $expiryDate] = $this->calculateDueAndExpiryDates($month);
    //         $monthRequest->merge([
    //             'ChallanMonth' => $monthString,
    //             'DueDate' => $dueDate,
    //             'ExpiryDate' => $expiryDate,
    //         ]);

    //         foreach ($request->StudentId as $value) {
    //             $firstMonthDate = $months->first()->format('Y-m') . '-01';
    //             $date = $monthString . "-01";
    //             $generateFeeChallanExist = GenerateFeeChallan::where('tenant_id', tenant('id'))
    //                 ->where('StudentId', $value['id'])
    //                 ->where('ChallanMonth', $date)
    //                 ->first();

    //             if ($generateFeeChallanExist) {
    //                 $studentData = Student::where('tenant_id', tenant('id'))
    //                     ->where('IsDisable', 0)
    //                     ->where('id', $generateFeeChallanExist->StudentId)
    //                     ->first(['FirstName']);

    //                 return throw ValidationException::withMessages([
    //                     'StudentId' => 'Challan already created for this student (' . $month->format('M-Y') . '): ' . ($studentData->FirstName ?? ''),
    //                 ]);
    //             }

    //             $challanNo = generateChallanNo();

    //             // For batch generation, do NOT create arrears/fines between months being generated
    //             // in the same request (e.g. May -> June). Only link arrears from challans that
    //             // existed before the batch start month.
    //             $previousChallan = null;
    //             if ($date === $firstMonthDate) {
    //                 $previousChallan = GenerateFeeChallan::where('tenant_id', tenant('id'))
    //                     ->where('StudentId', $value['id'])
    //                     ->where('ChallanMonth', '<', $firstMonthDate)
    //                     ->orderBy('ChallanMonth', 'desc')
    //                     ->orderBy('id', 'desc')
    //                     ->first();
    //             }

    //             $generateChallan = new GenerateFeeChallan();
    //             $generateChallan->tenant_id = tenant('id');
    //             $generateChallan->challan_no = $challanNo;
    //             $generateChallan->per_day_fine = $per_day_fine;
    //             $generateChallan->IsActive = 1;
    //             $generateChallan->StudentId = $value['id'];
    //             $generateChallan->ChallanMonth = $date;
    //             $generateChallan->DueDate = $dueDate;
    //             $generateChallan->SessionId = $currentSession->id;
    //             $generateChallan->ClassId = $request->ClassId;
    //             $generateChallan->SectionId = $request->SectionId;
    //             $generateChallan->ExpiryDate = $expiryDate;
    //             $generateChallan->Status = 'Unpaid';
    //             $generateChallan->IsPartialPayment = 0;
    //             $generateChallan->FineAmount = 0;
    //             $generateChallan->CreatedBy = auth()->user()->id;

    //             if ($generateChallan->save()) {
    //                 userActivityLogs('Generate Fee Challan and By User ID: '.auth()->user()->id.'', FeeLog::class);
    //                 $studentData = Student::select('id', 'tenant_id', 'FirstName', 'LastName')
    //                     ->with('userFCMToken')
    //                     ->where('tenant_id', tenant('id'))
    //                     ->where('id', $value['id'])
    //                     ->first();
    //                 $title = "New Challan Generated";
    //                 $body = "New challan generated for " . ($studentData->FirstName ?? '') . " " . ($studentData->LastName ?? '') . " successfully.";
    //                 try {
    //                     sendTestNotification($monthRequest, $title, $body, $studentData);
    //                 } catch (\Exception $e) {
    //                     \Log::error('FCM Error: ' . $e->getMessage());
    //                 }
    //             }

    //             if ($previousChallan) {
    //                 $this->checkArearsAndFines($previousChallan, $generateChallan);
    //             }

    //             $campusMasters = CampusFeesMaster::select('id', 'tenant_id', 'FeesTypeNId', 'Amount', 'ClassId')
    //                 ->Class($request->ClassId)
    //                 ->Tenant()
    //                 ->where('SessionId', $currentSession->id)
    //                 ->whereHas('FeeTypeRel')
    //                 ->with('FeeTypeRel')
    //                 ->get()
    //                 ->toArray();

    //             foreach ($campusMasters as $master) {
    //                 if ($master['fee_type_rel']['FeeCycle'] === 'Once') {
    //                     $this->isCampusMasterOnce($monthRequest, $generateChallan, $currentSession, $master, $value);
    //                 }
    //                 if ($master['fee_type_rel']['FeeCycle'] === 'Annually') {
    //                     $this->isCampusMasterAnnually($monthRequest, $generateChallan, $currentSession, $master, $value);
    //                 }
    //                 if ($master['fee_type_rel']['FeeCycle'] === 'Monthly') {
    //                     $this->isCampusMasterMonthly($generateChallan, $currentSession, $master, $value);
    //                 }
    //             }
    //         }
    //     }
    // }

    public function submitMultiMonthCombined($request)
    {
        $per_day_fine = getSiteMeta('Fine_Late_Fee');
        $currentSession = fetchCurrentSession();

        $months = collect($request->ChallanMonths ?? [])
            ->filter()
            ->map(function ($month) {
                return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            })
            ->sort()
            ->values();
        $startMonth = $months->first();
        $endMonth = $months->last();
        
        if (!$startMonth || !$endMonth) {
            return;
        }

        $startDate = $startMonth->format('Y-m') . '-01';
        $endDate = $endMonth->format('Y-m') . '-01';

        
        [$dueDate] = $this->calculateDueAndExpiryDates($startMonth);
        [, $expiryDate] = $this->calculateDueAndExpiryDates($endMonth);
        
        foreach ($request->StudentId as $value) {
            $datesToBlock = $months->map(fn ($m) => $m->format('Y-m') . '-01')->toArray();
            $existingAnyMonth = GenerateFeeChallan::where('tenant_id', tenant('id'))
            ->where('StudentId', $value['id'])
            ->whereIn('ChallanMonth', $datesToBlock)
            ->first();
        
            if ($existingAnyMonth) {
                $studentData = Student::where('tenant_id', tenant('id'))
                    ->where('IsDisable', 0)
                    ->where('id', $existingAnyMonth->StudentId)
                    ->first(['FirstName']);

                return throw ValidationException::withMessages([
                    'StudentId' => 'Challan already created for this student (' . $startMonth->format('M-Y') . ' to ' . $endMonth->format('M-Y') . '): ' . ($studentData->FirstName ?? ''),
                ]);
            }

            $existingCombined = GenerateFeeChallan::where('tenant_id', tenant('id'))
                ->where('StudentId', $value['id'])
                ->whereNotNull('Note')
                ->where('Note', 'like', '%Fee Cycle:%')
                ->orderBy('id', 'desc')
                ->get(['id', 'Note']);

            foreach ($existingCombined as $existingRow) {
                $range = $this->parseFeeCycleRange($existingRow->Note);
                if (!$range) {
                    continue;
                }
                [$existingStart, $existingEnd] = $range;
                // overlap if existingStart <= selectedEnd AND selectedStart <= existingEnd
                if ($existingStart->lte($endMonth) && $startMonth->lte($existingEnd)) {
                    $studentData = Student::where('tenant_id', tenant('id'))
                        ->where('IsDisable', 0)
                        ->where('id', $value['id'])
                        ->first(['FirstName']);

                    return throw ValidationException::withMessages([
                        'StudentId' => 'Challan already created for this student (Fee Cycle overlap): ' . ($studentData->FirstName ?? ''),
                    ]);
                }
            }

            // create ONE challan at end month date, with voucher range note
            $challanNo = generateChallanNo();
            $previousChallan = GenerateFeeChallan::where('tenant_id', tenant('id'))
                ->where('StudentId', $value['id'])
                ->where('ChallanMonth', '<', $startDate)
                ->orderBy('ChallanMonth', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $generateChallan = new GenerateFeeChallan();
            $generateChallan->tenant_id = tenant('id');
            $generateChallan->challan_no = $challanNo;
            $generateChallan->per_day_fine = $per_day_fine;
            $generateChallan->IsActive = 1;
            $generateChallan->StudentId = $value['id'];
            $generateChallan->ChallanMonth = $endDate;
            $generateChallan->DueDate = $dueDate;
            $generateChallan->SessionId = $currentSession->id;
            $generateChallan->ClassId = $request->ClassId;
            $generateChallan->SectionId = $request->SectionId;
            $generateChallan->ExpiryDate = $expiryDate;
            $generateChallan->Status = 'Unpaid';
            $generateChallan->IsPartialPayment = 0;
            $generateChallan->FineAmount = 0;
            $generateChallan->CreatedBy = auth()->user()->id;
            $generateChallan->Note = trim(($generateChallan->Note ?? '') . ' Fee Cycle: ' . $startMonth->format('M-Y') . ' to ' . $endMonth->format('M-Y') . ' (' . $startMonth->format('Y-m') . ' to ' . $endMonth->format('Y-m') . ')');

            if ($generateChallan->save()) {
                userActivityLogs('Generate Fee Challan and By User ID: '.auth()->user()->id.'', FeeLog::class);
            }

            if ($previousChallan) {
                $this->checkArearsAndFines($previousChallan, $generateChallan);
            }

            // create transactions as sum across months (monthly fee cycles multiplied)
            $campusMasters = CampusFeesMaster::select('id', 'tenant_id', 'FeesTypeNId', 'Amount', 'ClassId')
                ->Class($request->ClassId)
                ->Tenant()
                ->where('SessionId', $currentSession->id)
                ->whereHas('FeeTypeRel')
                ->with('FeeTypeRel')
                ->get()
                ->toArray();

            $monthsCount = $months->count();
            foreach ($campusMasters as $master) {
                if ($master['fee_type_rel']['FeeCycle'] === 'Once') {
                    $this->isCampusMasterOnce($request, $generateChallan, $currentSession, $master, $value);
                }
                if ($master['fee_type_rel']['FeeCycle'] === 'Annually') {
                    $this->isCampusMasterAnnuallyCombined($request, $generateChallan, $currentSession, $master, $value, $months);
                }
                if ($master['fee_type_rel']['FeeCycle'] === 'Monthly') {
                    $this->isCampusMasterMonthlyCombined($generateChallan, $currentSession, $master, $value, $monthsCount, $startMonth, $endMonth);
                }
            }
        }
    }

    private function parseFeeCycleRange(?string $note): ?array
    {
        if (!$note) {
            return null;
        }

        // Preferred: ISO range inside parentheses: (YYYY-MM to YYYY-MM)
        if (preg_match('/\\(([0-9]{4}-[0-9]{2})\\s+to\\s+([0-9]{4}-[0-9]{2})\\)/', $note, $m)) {
            try {
                $start = Carbon::createFromFormat('Y-m', $m[1])->startOfMonth();
                $end = Carbon::createFromFormat('Y-m', $m[2])->startOfMonth();
                return [$start, $end];
            } catch (\Exception $e) {
                return null;
            }
        }

        // Fallback: "Fee Cycle: Mon-YYYY to Mon-YYYY"
        if (preg_match('/Fee Cycle:\\s*([A-Za-z]{3}-[0-9]{4})\\s+to\\s+([A-Za-z]{3}-[0-9]{4})/', $note, $m)) {
            try {
                $start = Carbon::createFromFormat('M-Y', $m[1])->startOfMonth();
                $end = Carbon::createFromFormat('M-Y', $m[2])->startOfMonth();
                return [$start, $end];
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    private function isCampusMasterMonthlyCombined($generateChallan, $currentSession, $master, $value, int $monthsCount, Carbon $startMonth, Carbon $endMonth)
    {
        $TotalFee = $master['Amount'] * $monthsCount;
        $BalanceFeeAfterDiscount = $master['Amount'] * $monthsCount;
        $DiscountAmount = 0;

        $studentFeeDiscountAvalible = StudentFeeDiscount::where('tenant_id', tenant('id'))
            ->where('StudentId', $value['id'])
            ->where('CampusFeesMasterId', $master['id'])
            ->first();

        if ($studentFeeDiscountAvalible) {
            $TotalFee = ($studentFeeDiscountAvalible['TotalFee'] ?? $master['Amount']) * $monthsCount;
            $DiscountAmount = $studentFeeDiscountAvalible['DiscountAmount'] ?? 0;
            $BalanceFeeAfterDiscount = ($studentFeeDiscountAvalible['BalanceFeeAfterDiscount'] ?? $master['Amount']) * $monthsCount;
        }

        $challanTransactionsCreate = new ChallanTransactions();
        $challanTransactionsCreate->tenant_id = tenant('id');
        $challanTransactionsCreate->generate_challan_id = $generateChallan->id;
        $challanTransactionsCreate->CreatedBy = auth()->user()->id;
        $challanTransactionsCreate->SessionId = $currentSession->id;
        $challanTransactionsCreate->FeeAmount = $master['Amount'] * $monthsCount;
        $challanTransactionsCreate->FKey = $master['fee_type_rel']['id'];
        $challanTransactionsCreate->TransactionType =  $master['fee_type_rel']['FeeName'];
        // $challanTransactionsCreate->TransactionType =  ($master['fee_type_rel']['FeeName'] ?? '') . ' (' . $startMonth->format('M-Y') . ' to ' . $endMonth->format('M-Y') . ')';
        $challanTransactionsCreate->challan_type = 'Monthly Challan';
        $challanTransactionsCreate->DiscountAmount = $DiscountAmount;
        $challanTransactionsCreate->BalanceFeeAfterDiscount = $BalanceFeeAfterDiscount;
        $challanTransactionsCreate->TotalFee = $TotalFee;
        $challanTransactionsCreate->save();
    }

    private function calculateDueAndExpiryDates(Carbon $month): array
    {
        $dueDate = $month->copy()->day(10);
        // JS parity: Sat(+2), Sun(+1)
        if ($dueDate->dayOfWeek === Carbon::SATURDAY) {
            $dueDate->addDays(2);
        } elseif ($dueDate->dayOfWeek === Carbon::SUNDAY) {
            $dueDate->addDay();
        }

        $expiryDate = $month->copy()->endOfMonth();
        return [$dueDate->toDateString(), $expiryDate->toDateString()];
    }

    private function isCampusMasterAnnually($request, $generateChallan, $currentSession, $master, $value)
    {
        $TotalFee = $master['Amount'];
        $BalanceFeeAfterDiscount = $master['Amount'];
        $DiscountAmount = 0;
        if ($master['fee_type_rel']['IsOptional'] === true) {
            $date = $request->ChallanMonth . "-01";
            $optionalFeeMaster = OptionalFeeMaster::where('ClassId', $request->ClassId)
                ->where('FeesTypeNId', $master['fee_type_rel']['id'])
                ->where('StudentId', $value['id'])
                ->where('tenant_id', tenant('id'))
                ->whereDate('FromMonth', '<=', $date)
                ->whereDate('ToMonth', '>=', $date)
                ->first();

            if ($optionalFeeMaster) {

                $studentFeeDiscountAvalible = StudentFeeDiscount::where('tenant_id', tenant('id'))->where('StudentId', $value['id'])->where('CampusFeesMasterId', $optionalFeeMaster->CampusFeesMasterId)->first();
                if ($studentFeeDiscountAvalible) {
                    $TotalFee = $studentFeeDiscountAvalible['TotalFee'];
                    $DiscountAmount = $studentFeeDiscountAvalible['DiscountAmount'];
                    $BalanceFeeAfterDiscount = $studentFeeDiscountAvalible['BalanceFeeAfterDiscount'];

                    $challanTransactionsCreate = new ChallanTransactions();
                    $challanTransactionsCreate->tenant_id = tenant('id');
                    $challanTransactionsCreate->generate_challan_id = $generateChallan->id;
                    $challanTransactionsCreate->CreatedBy = auth()->user()->id;
                    $challanTransactionsCreate->SessionId = $currentSession->id;
                    $challanTransactionsCreate->FeeAmount = $master['Amount'];
                    $challanTransactionsCreate->FKey = $master['fee_type_rel']['id'];
                    $challanTransactionsCreate->TransactionType = $master['fee_type_rel']['FeeName'];
                    $challanTransactionsCreate->challan_type = 'Optional Annually Challan with Discount';
                    $challanTransactionsCreate->DiscountAmount = $DiscountAmount;
                    $challanTransactionsCreate->BalanceFeeAfterDiscount = $BalanceFeeAfterDiscount;
                    $challanTransactionsCreate->TotalFee = $TotalFee;
                    $challanTransactionsCreate->save();
                } else {

                    $challanTransactionsCreate = new ChallanTransactions();
                    $challanTransactionsCreate->tenant_id = tenant('id');
                    $challanTransactionsCreate->generate_challan_id = $generateChallan->id;
                    $challanTransactionsCreate->CreatedBy = auth()->user()->id;
                    $challanTransactionsCreate->SessionId = $currentSession->id;
                    $challanTransactionsCreate->FeeAmount = $master['Amount'];
                    $challanTransactionsCreate->FKey = $master['fee_type_rel']['id'];
                    $challanTransactionsCreate->TransactionType = $master['fee_type_rel']['FeeName'];
                    $challanTransactionsCreate->challan_type = 'Optional Annually Challan without Discount';
                    $challanTransactionsCreate->DiscountAmount = 0;
                    $challanTransactionsCreate->BalanceFeeAfterDiscount = $BalanceFeeAfterDiscount;
                    $challanTransactionsCreate->TotalFee = $TotalFee;
                    $challanTransactionsCreate->save();
                }
            }
        } else {

            $existingAnnuallyChallan = ChallanTransactions::where('tenant_id', tenant('id'))
                ->where('challan_type', 'Annually Challan')
                ->where('SessionId', $currentSession->id)
                ->where('FKey', $master['fee_type_rel']['id'])
                ->whereHas('generateChallan', function ($q) use ($value, $currentSession) {
                    $q->where('StudentId', $value['id'])
                        ->where('SessionId', $currentSession->id);
                })
                ->first();

            if (!$existingAnnuallyChallan) {
                $studentFeeDiscountAvalible = StudentFeeDiscount::where('tenant_id', tenant('id'))->where('StudentId', $value['id'])->where('CampusFeesMasterId', $master['id'])->first();
                if ($studentFeeDiscountAvalible) {
                    $TotalFee = $studentFeeDiscountAvalible['TotalFee'];
                    $DiscountAmount = $studentFeeDiscountAvalible['DiscountAmount'];
                    $BalanceFeeAfterDiscount = $studentFeeDiscountAvalible['BalanceFeeAfterDiscount'];

                    $challanTransactionsCreate = new ChallanTransactions();
                    $challanTransactionsCreate->tenant_id = tenant('id');
                    $challanTransactionsCreate->generate_challan_id = $generateChallan->id;
                    $challanTransactionsCreate->CreatedBy = auth()->user()->id;
                    $challanTransactionsCreate->SessionId = $currentSession->id;
                    $challanTransactionsCreate->FeeAmount = $master['Amount'];
                    $challanTransactionsCreate->FKey = $master['fee_type_rel']['id'];
                    $challanTransactionsCreate->TransactionType = $master['fee_type_rel']['FeeName'];
                    $challanTransactionsCreate->challan_type = 'Annually Challan';
                    $challanTransactionsCreate->DiscountAmount = $DiscountAmount;
                    $challanTransactionsCreate->BalanceFeeAfterDiscount = $BalanceFeeAfterDiscount;
                    $challanTransactionsCreate->TotalFee = $TotalFee;
                    $challanTransactionsCreate->save();
                } else {
                    $challanTransactionsCreate = new ChallanTransactions();
                    $challanTransactionsCreate->tenant_id = tenant('id');
                    $challanTransactionsCreate->generate_challan_id = $generateChallan->id;
                    $challanTransactionsCreate->CreatedBy = auth()->user()->id;
                    $challanTransactionsCreate->SessionId = $currentSession->id;
                    $challanTransactionsCreate->FeeAmount = $master['Amount'];
                    $challanTransactionsCreate->FKey = $master['fee_type_rel']['id'];
                    $challanTransactionsCreate->TransactionType = $master['fee_type_rel']['FeeName'];
                    $challanTransactionsCreate->challan_type = 'Annually Challan';
                    $challanTransactionsCreate->DiscountAmount = 0;
                    $challanTransactionsCreate->BalanceFeeAfterDiscount = $BalanceFeeAfterDiscount;
                    $challanTransactionsCreate->TotalFee = $TotalFee;
                    $challanTransactionsCreate->save();
                }
            }
        }
    }

    private function isCampusMasterAnnuallyCombined($request, $generateChallan, $currentSession, $master, $value, $months)
    {
        if ($master['fee_type_rel']['IsOptional'] === true) {
            $mappedMonths = collect($months)->filter(function ($month) use ($request, $master, $value) {
                $date = $month->format('Y-m') . '-01';
                return OptionalFeeMaster::where('ClassId', $request->ClassId)
                    ->where('FeesTypeNId', $master['fee_type_rel']['id'])
                    ->where('StudentId', $value['id'])
                    ->where('tenant_id', tenant('id'))
                    ->whereDate('FromMonth', '<=', $date)
                    ->whereDate('ToMonth', '>=', $date)
                    ->exists();
            });
            
            $mappedMonthsCount = $mappedMonths->count();
            if ($mappedMonthsCount === 0) {
                return;
            }

            $studentFeeDiscountAvalible = StudentFeeDiscount::where('tenant_id', tenant('id'))
                ->where('StudentId', $value['id'])
                ->where('CampusFeesMasterId', $master['id'])
                ->first();

            $TotalFee = ($studentFeeDiscountAvalible['TotalFee'] ?? $master['Amount']) * $mappedMonthsCount;
            $DiscountAmount = ($studentFeeDiscountAvalible['DiscountAmount'] ?? 0) * $mappedMonthsCount;
            $BalanceFeeAfterDiscount = ($studentFeeDiscountAvalible['BalanceFeeAfterDiscount'] ?? $master['Amount']) * $mappedMonthsCount;

            $challanTransactionsCreate = new ChallanTransactions();
            $challanTransactionsCreate->tenant_id = tenant('id');
            $challanTransactionsCreate->generate_challan_id = $generateChallan->id;
            $challanTransactionsCreate->CreatedBy = auth()->user()->id;
            $challanTransactionsCreate->SessionId = $currentSession->id;
            $challanTransactionsCreate->FeeAmount = $master['Amount'] * $mappedMonthsCount;
            $challanTransactionsCreate->FKey = $master['fee_type_rel']['id'];
            $challanTransactionsCreate->TransactionType = $master['fee_type_rel']['FeeName'];
            $challanTransactionsCreate->challan_type = $studentFeeDiscountAvalible
                ? 'Optional Annually Challan with Discount'
                : 'Optional Annually Challan without Discount';
            $challanTransactionsCreate->DiscountAmount = $DiscountAmount;
            $challanTransactionsCreate->BalanceFeeAfterDiscount = $BalanceFeeAfterDiscount;
            $challanTransactionsCreate->TotalFee = $TotalFee;
            $challanTransactionsCreate->save();
            return;
        }

        $this->isCampusMasterAnnually($request, $generateChallan, $currentSession, $master, $value);
    }

    private function isCampusMasterMonthly($generateChallan, $currentSession, $master, $value)
    {
        $TotalFee = $master['Amount'];
        $BalanceFeeAfterDiscount = $master['Amount'];;
        $DiscountAmount = 0;

        $studentFeeDiscountAvalible = StudentFeeDiscount::where('tenant_id', tenant('id'))->where('StudentId', $value['id'])->where('CampusFeesMasterId', $master['id'])->first();

        if ($studentFeeDiscountAvalible) {
            $TotalFee = $studentFeeDiscountAvalible['TotalFee'];
            $DiscountAmount = $studentFeeDiscountAvalible['DiscountAmount'];
            $BalanceFeeAfterDiscount = $studentFeeDiscountAvalible['BalanceFeeAfterDiscount'];
        }

        $challanTransactionsCreate = new ChallanTransactions();
        $challanTransactionsCreate->tenant_id = tenant('id');
        $challanTransactionsCreate->generate_challan_id = $generateChallan->id;
        $challanTransactionsCreate->CreatedBy = auth()->user()->id;
        $challanTransactionsCreate->SessionId = $currentSession->id;
        $challanTransactionsCreate->FeeAmount = $master['Amount'];
        $challanTransactionsCreate->FKey = $master['fee_type_rel']['id'];
        $challanTransactionsCreate->TransactionType = $master['fee_type_rel']['FeeName'];
        $challanTransactionsCreate->challan_type = 'Monthly Challan';
        $challanTransactionsCreate->DiscountAmount = $DiscountAmount;
        $challanTransactionsCreate->BalanceFeeAfterDiscount = $BalanceFeeAfterDiscount;
        $challanTransactionsCreate->TotalFee = $TotalFee;
        $challanTransactionsCreate->save();
    }

    private function isCampusMasterOnce($request, $generateChallan, $currentSession, $master, $value)
    {
        $existingOnceChallan = ChallanTransactions::where('tenant_id', tenant('id'))
            ->where('challan_type', 'Once Challan')
            ->where('FKey', $master['fee_type_rel']['id'])
            ->whereHas('generateChallan', function ($q) use ($value) {
                $q->where('StudentId', $value['id']);
            })
            ->first();

        if (!$existingOnceChallan) {

            $TotalFee = $master['Amount'];
            $BalanceFeeAfterDiscount = $master['Amount'];;
            $DiscountAmount = 0;

            $studentFeeDiscountAvalible = StudentFeeDiscount::where('tenant_id', tenant('id'))->where('StudentId', $value['id'])->where('CampusFeesMasterId', $master['id'])->first();

            if ($studentFeeDiscountAvalible) {
                $TotalFee = $studentFeeDiscountAvalible['TotalFee'];
                $DiscountAmount = $studentFeeDiscountAvalible['DiscountAmount'];
                $BalanceFeeAfterDiscount = $studentFeeDiscountAvalible['BalanceFeeAfterDiscount'];
            }

            $challanTransactionsCreate = new ChallanTransactions();
            $challanTransactionsCreate->tenant_id = tenant('id');
            $challanTransactionsCreate->generate_challan_id = $generateChallan->id;
            $challanTransactionsCreate->CreatedBy = auth()->user()->id;
            $challanTransactionsCreate->SessionId = $currentSession->id;
            $challanTransactionsCreate->FeeAmount = $master['Amount'];
            $challanTransactionsCreate->FKey = $master['fee_type_rel']['id'];
            $challanTransactionsCreate->TransactionType = $master['fee_type_rel']['FeeName'];
            $challanTransactionsCreate->challan_type = 'Once Challan';
            $challanTransactionsCreate->DiscountAmount = $DiscountAmount;
            $challanTransactionsCreate->BalanceFeeAfterDiscount = $BalanceFeeAfterDiscount;
            $challanTransactionsCreate->TotalFee = $TotalFee;
            $challanTransactionsCreate->save();
        }
    }

    private function checkArearsAndFines($previousChallan, $generateChallan)
    {
        $previousStatus = strtolower(trim((string) $previousChallan->Status));
        $isPartialPayment = (int) $previousChallan->IsPartialPayment;

        if ($previousStatus === 'unpaid' && $isPartialPayment === 0) {
            $ArrearChallanDetails = ArrearChallanDetails::where('GenerateFeeChallanId', $previousChallan->id)->get();
            if (count($ArrearChallanDetails) > 0) {
                $insertArrearChallanDetails = [];
                foreach ($ArrearChallanDetails as $key => $detailId) {
                    $insertArrearChallanDetails[$key]['tenant_id'] = tenant('id');
                    $insertArrearChallanDetails[$key]['GenerateFeeChallanId'] = $generateChallan['id'];
                    $insertArrearChallanDetails[$key]['FKeyId'] = $detailId['FKeyId'];
                    $insertArrearChallanDetails[$key]['TransactionType'] = $detailId['TransactionType'];
                    $insertArrearChallanDetails[$key]['CreatedBy'] = auth()->user()->id;
                    $insertArrearChallanDetails[$key]['SessionId'] = $previousChallan->SessionId;
                    $insertArrearChallanDetails[$key]['is_partial_payment'] = $detailId['is_partial_payment'];
                    $insertArrearChallanDetails[$key]['created_at'] = now();
                    $insertArrearChallanDetails[$key]['updated_at'] = now();
                }
                ArrearChallanDetails::insert($insertArrearChallanDetails);
            }

            $ArrearChallanDetals = new ArrearChallanDetails();
            $ArrearChallanDetals->tenant_id = tenant('id');
            $ArrearChallanDetals->GenerateFeeChallanId = $generateChallan->id;
            $ArrearChallanDetals->FKeyId = $previousChallan->id;
            $ArrearChallanDetals->TransactionType = 'Arrears';
            $ArrearChallanDetals->CreatedBy = auth()->user()->id;
            $ArrearChallanDetals->SessionId = $previousChallan->SessionId;
            $ArrearChallanDetals->save();
        }

        if ($previousStatus === 'unpaid' && $isPartialPayment === 1) {
            $ArrearChallanDetails = ArrearChallanDetails::where('GenerateFeeChallanId', $previousChallan->id)->get();
            if (count($ArrearChallanDetails) > 0) {
                $insertArrearChallanDetails = [];
                foreach ($ArrearChallanDetails as $key => $detailId) {
                    $insertArrearChallanDetails[$key]['tenant_id'] = tenant('id');
                    $insertArrearChallanDetails[$key]['GenerateFeeChallanId'] = $generateChallan['id'];
                    $insertArrearChallanDetails[$key]['FKeyId'] = $detailId['FKeyId'];
                    $insertArrearChallanDetails[$key]['TransactionType'] = $detailId['TransactionType'];
                    $insertArrearChallanDetails[$key]['CreatedBy'] = auth()->user()->id;
                    $insertArrearChallanDetails[$key]['SessionId'] = $previousChallan->SessionId;
                    $insertArrearChallanDetails[$key]['is_partial_payment'] = 0;
                    $insertArrearChallanDetails[$key]['created_at'] = now();
                    $insertArrearChallanDetails[$key]['updated_at'] = now();
                }
                ArrearChallanDetails::insert($insertArrearChallanDetails);
            }

            $ArrearChallanDetals = new ArrearChallanDetails();
            $ArrearChallanDetals->tenant_id = tenant('id');
            $ArrearChallanDetals->GenerateFeeChallanId = $generateChallan->id;
            $ArrearChallanDetals->FKeyId = $previousChallan->id;
            $ArrearChallanDetals->TransactionType = 'Arrears';
            $ArrearChallanDetals->CreatedBy = auth()->user()->id;
            $ArrearChallanDetals->SessionId = $previousChallan->SessionId;
            $ArrearChallanDetals->is_partial_payment = 1;
            $ArrearChallanDetals->save();
        }

        if ($previousStatus === 'paid') {
            if ($previousChallan->DueDate->toDateString() < $previousChallan->SubmitDate) {
                $ArrearChallanDetals = new ArrearChallanDetails();
                $ArrearChallanDetals->tenant_id = tenant('id');
                $ArrearChallanDetals->GenerateFeeChallanId = $generateChallan->id;
                $ArrearChallanDetals->FKeyId = $previousChallan->id;
                $ArrearChallanDetals->TransactionType = 'Fine';
                $ArrearChallanDetals->CreatedBy = auth()->user()->id;
                $ArrearChallanDetals->SessionId = $previousChallan->SessionId;
                $ArrearChallanDetals->save();
            }
        }
    }

    public function print($request): array
    {
        $tenant_id = tenant('id');
        if(session('switched_tenant_id')){
            $tenant_id = session('switched_tenant_id');
        }

        $ids = explode(',', $request->id);
        $challan = GenerateFeeChallan::with('ChallanTransactions', 'StudentRel', 'ClassRel','SectionRel', 'ChallanArrearsRel')
        ->whereIn('id', $ids)
        ->where('tenant_id', $tenant_id)
        ->get();
        $challans = $challan->toArray();
        $feeCycles = [];
        foreach ($challans as $c) {
            if (!empty($c['Note']) && str_contains($c['Note'], 'Fee Cycle:')) {
                $feeCycles[$c['id']] = trim(substr($c['Note'], strpos($c['Note'], 'Fee Cycle:')));
            }
        }
        $previousChallanData = [];
        $waivedOffByChallan = [];
        foreach ($challans as $challnList) {
            $waivedOffByChallan[$challnList['id']] = 0;
            if (isset($challnList['challan_arrears_rel']) || !empty($challnList['challan_arrears_rel'])) {
                foreach ($challnList['challan_arrears_rel'] as $arrears) {
                    $generateChallanFineList = GenerateFeeChallan::where('id', $arrears['FKeyId'])->where('tenant_id', $tenant_id)->first();
                    if ($arrears['TransactionType'] == 'Arrears' && $generateChallanFineList && $generateChallanFineList->IsPartialPayment == 0) {
                        $ChallanTransactionsSum = $this->ChallanTransactionsSum($arrears['FKeyId']);
                        $ChallanTransactionsDueMonth = $this->ChallanTransactionsDueMonth($arrears['FKeyId']);
                        if (!$ChallanTransactionsDueMonth || !isset($ChallanTransactionsDueMonth['ChallanMonth'])) {
                            continue;
                        }
                        $formatedMonth = Carbon::parse($ChallanTransactionsDueMonth['ChallanMonth'])->format('M-Y');
                        // $dueDate = Carbon::createFromFormat('M-Y-d', $challnList['DueDate']);
                        // $expiryDate = Carbon::createFromFormat('M-d-Y', $challnList['ExpiryDate']);
                        // $days = $dueDate->diffInDays($expiryDate);

                        $dueDate = Carbon::parse($generateChallanFineList['DueDate']);
                        $expiryDate = Carbon::parse($generateChallanFineList['ExpiryDate']);
                        $days = $dueDate->diffInDays($expiryDate);
                        $totalFine = $days * $generateChallanFineList['per_day_fine'];  
                        $waivedOffByChallan[$challnList['id']] += $generateChallanFineList['WaivedFineAmount'] ? $generateChallanFineList['WaivedFineAmount'] : 0;
                        $previousChallanData[$challnList['id']][] = [
                           'challan_id' => $arrears['FKeyId'] ? $arrears['FKeyId'] : 0,
                            'total_amount' => $ChallanTransactionsSum,
                            'total_fine' => $generateChallanFineList['per_day_fine'] ? $totalFine : $generateChallanFineList['FineAmount'],
                            'ChallanMonth' => $formatedMonth,
                            'has_arraear' => 'no',
                        ];
                    }
                    if ($arrears['TransactionType'] == 'Arrears' && $generateChallanFineList && $generateChallanFineList->IsPartialPayment == 1) {
                        $ChallanTransactionsSum = $this->ChallanTransactionsSum($arrears['FKeyId']);
                        $ChallanTransactionsDueMonth = $this->ChallanTransactionsDueMonth($arrears['FKeyId']);
                        if (!$ChallanTransactionsDueMonth || !isset($ChallanTransactionsDueMonth['ChallanMonth'])) {
                            continue;
                        }
                        $formatedMonth = Carbon::parse($ChallanTransactionsDueMonth['ChallanMonth'])->format('M-Y');

                        $ChallanPartialPaymentSum = ChallanPartialPayment::where('GenerateClassChallanId', $arrears['FKeyId'])->sum('ReceivedAmount');
                        $dueDate = Carbon::parse($generateChallanFineList['DueDate']);
                        $expiryDate = Carbon::parse($generateChallanFineList['ExpiryDate']);
                        $days = $dueDate->diffInDays($expiryDate);
                        $totalFine = $days * $generateChallanFineList['per_day_fine'];
                        $waivedOffByChallan[$challnList['id']] += $generateChallanFineList['WaivedFineAmount'] ? $generateChallanFineList['WaivedFineAmount'] : 0;
                        $previousChallanData[$challnList['id']][] = [
                            'challan_id' => $arrears['FKeyId'] ? $arrears['FKeyId'] : 0,
                            'total_amount' => $ChallanTransactionsSum - $ChallanPartialPaymentSum,
                            'total_fine' => $generateChallanFineList['per_day_fine'] ? $totalFine : $generateChallanFineList['FineAmount'],
                            'ChallanMonth' => $formatedMonth,
                            'has_arraear' => 'yes',
                        ];
                    }

                    if ($arrears['TransactionType'] == 'Fine') {
                        $fineAmount = $this->ChallanTransactionsDueMonth($arrears['FKeyId']);

                        $previousChallanData[$challnList['id']][] = [
                            'challan_id' => $arrears['FKeyId'] ? $arrears['FKeyId'] : 0,
                            'wived_off' => 0,
                            'total_amount' => 0,
                            'total_fine' => $fineAmount->FineAmount,
                            'ChallanMonth' => $fineAmount->ChallanMonth->toDateString(),
                            'has_arraear' => 'no',
                        ];
                    }
                }
            }
        }

        $data['challans'] = $challans;
        $data['previousChallanData'] = $previousChallanData;
        $data['waivedOffByChallan'] = $waivedOffByChallan;
        $data['feeCycles'] = $feeCycles;
        // dd($data);
        return $data;
    }

    private function ChallanTransactionsSum($FKeyId): float | ChallanTransactions
    {
        return ChallanTransactions::where('generate_challan_id', $FKeyId)->sum('BalanceFeeAfterDiscount');
    }
    private function ChallanTransactionsDueMonth($FKeyId): GenerateFeeChallan | NULL
    {
        $tenant_id = tenant('id');
        if(session('switched_tenant_id')){
            $tenant_id = session('switched_tenant_id');
        }
        return GenerateFeeChallan::select('id', 'tenant_id', 'challan_no', 'ChallanMonth', 'WaivedFineAmount', 'FineAmount')
            // ->withTrashed()
            ->where('tenant_id', $tenant_id)
            ->where('id', $FKeyId)->first();
    }

    public function markAsUnpaid($request)
    {
        $tenant_id = tenant('id');
        if(session('switched_tenant_id')){
            $tenant_id = session('switched_tenant_id');
        }
        $challan = GenerateFeeChallan::where('tenant_id',$tenant_id)->findOrFail($request->id);
        $nextChallan = GenerateFeeChallan::where('tenant_id',$tenant_id)
            ->where('StudentId', $challan->StudentId)
            ->whereDate('ChallanMonth', '>', $challan->ChallanMonth)
            ->orderBy('ChallanMonth', 'asc')
                ->first();
        
        if($nextChallan){
            return false;
        }else{
            $challan->update([
                'Status' => 'Unpaid',
                'SubmitDate' => NULL,
                'PaymentMode' => NULL,
                'CollectDate' => NULL,
                'CollectBy' => NULL,
            ]);
             userActivityLogs('Challan Mark as Unpaid and Challan Id is '.$challan->id.' And Challan Mark as Unpaid By User ID '.auth()->user()->id.' ', FeeLog::class);
            return true;
        }

        

    }
}
