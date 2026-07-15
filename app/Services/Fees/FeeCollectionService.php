<?php

namespace App\Services\Fees;

use App\Models\ChallanPartialPayment;
use App\Models\ChallanTransactions;
use App\Models\GenerateFeeChallan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class FeeCollectionService
{
    public function index($request): array
    {
        $query = GenerateFeeChallan::where('tenant_id', tenant('id'))
            ->where('Status', 'Paid')
            ->with('student.section', 'student.class')
            ->withSum('partialPayments', 'ReceivedAmount')
            ->withSum('transection', 'BalanceFeeAfterDiscount')
            ->orderBy('challan_no', 'desc');

        // if ($request->filled('search')) {
        //     $query->where('challan_no', 'like', '%' . $request->search . '%');
        // }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // dd($search);
                $q->where('challan_no', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sub) use ($search) {
                        $sub->where('FirstName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student.class', function ($sub) use ($search) {
                        $sub->where('ClassName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student.section', function ($sub) use ($search) {
                        $sub->where('SectionName', 'like', "%{$search}%");
                    });
            });
        }

        $data['GenerateFeeChallan'] = $query->paginate(25)->withQueryString();
        $data['filters'] = $request->only('search');

        return $data;
    }

    public function create($validated): array|RedirectResponse
    {

        $data['paidChallan'] = GenerateFeeChallan::where('tenant_id', tenant('id'))
            ->where('challan_no', (int) $validated['ChallanNo'])
            ->where('Status', 'Paid')
                // ->where('IsPartialPayment', 0)
            ->first();
        // dd($data['paidChallan']);
        if (isset($data['paidChallan'])) {
            $data['error'] = 'This challan has already been fully paid....';

            return $data;
        }

        $data = $this->getChallanData($validated['ChallanNo']);

        if (isset($data['error'])) {
            return ['error' => 'Challan not found.'];
        }

        $latestChallan = GenerateFeeChallan::with('ChallanArrearsRel')
            ->where('StudentId', $data['GenerateFeeChallan']->StudentId)
            ->where('Status', 'Unpaid')
            ->latest('challan_no')
            ->first();

        if ($latestChallan && $latestChallan->id !== $data['GenerateFeeChallan']->id) {
            $data['latestChallan'] = "⚠️ Please Collect Latest challan #. {$latestChallan->challan_no}";
        }

        $latestChallanData = GenerateFeeChallan::with('ChallanArrearsRel')
            ->where('tenant_id', tenant('id'))
            ->where('challan_no', (int) $validated['ChallanNo'])
            ->where('Status', 'Unpaid')
            ->first();

        $wived_off = 0;
        foreach ($latestChallanData['ChallanArrearsRel'] as $arrears) {
            $generateChallanFineList = GenerateFeeChallan::where('id', $arrears['FKeyId'])->where('tenant_id', tenant('id'))->first();
            if ($arrears['TransactionType'] == 'Arrears' && $generateChallanFineList->IsPartialPayment == 0) {
                $wived_off += $generateChallanFineList['WaivedFineAmount'] ? $generateChallanFineList['WaivedFineAmount'] : 0;
            }
            if ($arrears['TransactionType'] == 'Arrears' && $generateChallanFineList->IsPartialPayment == 1) {
                $wived_off += $generateChallanFineList['WaivedFineAmount'] ? $generateChallanFineList['WaivedFineAmount'] : 0;
            }
        }
        $data['wived_off'] = $wived_off;
        // dd($data['TotalReceivedAmount']);
        // $TotalReceivedAmount = $data['TotalReceivedAmount'] + (int) $data['GenerateFeeChallan']->WaivedFineAmount;

        // if ($TotalReceivedAmount == $data['GenerateFeeChallan']->transection_sum_balancefeeafterdiscount && $data['GenerateFeeChallan']->transection_sum_balancefeeafterdiscount != 0) {
        //     $data['error'] = 'This challan has already been fully paid.';
        // }

        return $data;
    }

    public function submit($request): array
    {
        $tenantId = tenant('id');
        if (session('switched_tenant_id')) {
            $tenantId = session('switched_tenant_id');
        }

        return DB::transaction(function () use ($request, $tenantId) {
            $validated = $request->validated();

            $challanBefore = GenerateFeeChallan::where('tenant_id', $tenantId)
                ->withSum('transection', 'BalanceFeeAfterDiscount')
                ->findOrFail($validated['GenerateClassChallanId']);

            $alreadyPaidBefore = (float) ChallanPartialPayment::where('tenant_id', $tenantId)
                ->where('GenerateClassChallanId', $validated['GenerateClassChallanId'])
                ->sum('ReceivedAmount');

            $waivedTotal = (float) ($validated['waived_amount'] ?? 0);
            $waivedNow = $waivedTotal - (float) ($challanBefore->WaivedFineAmount ?? 0);

            $fineTotal = (float) ($request->fine_amount ?? 0);
            $fineNow = $fineTotal - (float) ($challanBefore->FineAmount ?? 0);

            $receivedNow = (float) $validated['ReceivedAmount'];
            $payableAmount = (float) $validated['balance_after_discount'];

            $totalReceivedForSettlement = round($receivedNow + $waivedTotal, 2);
            $payableAmountRounded = round($payableAmount, 2);

            $partialPayment = null;
            if ($totalReceivedForSettlement == $payableAmountRounded) {
                $this->updateGenerateFeeChallan($validated['GenerateClassChallanId'], $request);
            } else {
                $data = collect($validated)->except(['waived_amount', 'balance_after_discount'])->toArray();
                $data['tenant_id'] = $tenantId;
                $data['CreatedBy'] = auth()->user()->id;
                $data['CollectDate'] = now();
                $data['CollectBy'] = auth()->user()->id;
                $partialPayment = ChallanPartialPayment::create($data);
                $this->updateChallanStatus($validated['GenerateClassChallanId'], $request);
            }

            $alreadyPaidAfter = $alreadyPaidBefore + $receivedNow;
            $balanceAfter = $payableAmount - $alreadyPaidAfter - $waivedTotal;

            $receiptNo = 'RCPT-'.now()->format('Ymd-His').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            return [
                'tenant_id' => $tenantId,
                'receipt_no' => $receiptNo,
                'generate_fee_challan_id' => $challanBefore->id,
                'challan_partial_payment_id' => $partialPayment?->id,
                'student_id' => $challanBefore->StudentId,
                'challan_no' => $challanBefore->challan_no,
                'payment_type' => $partialPayment ? 'partial' : 'full',
                'payable_amount' => round($payableAmount, 2),
                'already_paid_before' => round($alreadyPaidBefore, 2),
                'received_amount' => round($receivedNow, 2),
                'waived_amount' => round(max(0, $waivedNow), 2),
                'waived_amount_total' => round($waivedTotal, 2),
                'fine_amount' => round(max(0, $fineNow), 2),
                'fine_amount_total' => round($fineTotal, 2),
                'balance_after' => round(max(0, $balanceAfter), 2),
                'payment_mode' => $validated['PaymentMode'] ?? null,
                'submit_date' => $validated['SubmitDate'] ?? null,
                // 'note' => $validated['note'] ?? null,
                'collected_at' => now()->toDateTimeString(),
                'collected_by' => auth()->id(),
                'collected_by_name' => auth()->user()?->name,
            ];
        });
    }

    public function show($validated): array
    {
        $data = $this->getChallanData($validated['ChallanNo']);
        if (! $data) {
            return ['error' => 'No challan found.'];
        }

        return $data;
    }

    public function delete($id): void
    {
        $challanItem = ChallanPartialPayment::findOrFail($id);
        $challanItem->delete();
        $challanItem->update(['IsActive' => 0]);
        $remainingPayments = ChallanPartialPayment::where('GenerateClassChallanId', $challanItem->GenerateClassChallanId)->count();
        if ($remainingPayments === 0) {
            GenerateFeeChallan::where('id', $challanItem->GenerateClassChallanId)
                ->update([
                    'IsPartialPayment' => false,
                    'ModifiedBy' => auth()->user()->id,
                    'Status' => 'Unpaid',
                    'PaymentMode' => null,
                    'WaivedFineAmount' => 0,
                ]);
        }
    }

    protected function getChallanData($challanNo): ?array
    {
        $data['GenerateFeeChallan'] = GenerateFeeChallan::where('tenant_id', tenant('id'))
            ->where('challan_no', $challanNo)
            ->with([
                'student.section',
                'student.class',
                'partialPayments.feeChallan.student.section',
                'partialPayments.feeChallan.student.class',
            ])
            ->withSum('transection', 'BalanceFeeAfterDiscount')
            ->first();

        if (! $data['GenerateFeeChallan']) {
            return ['error' => 'Challan not found.'];
        }

        $data['PartialChallanList'] = $data['GenerateFeeChallan']->partialPayments;
        $data['TotalReceivedAmount'] = (int) $data['GenerateFeeChallan']->partialPayments->sum('ReceivedAmount');
        $data['dueDate'] = $data['GenerateFeeChallan']->getRawOriginal('DueDate') ?? null;
        $data['FineLateFee'] = getSiteMeta('Fine_Late_Fee');

        $data['total_challan_amount'] = $this->getChallanArrearsAndFine($data['GenerateFeeChallan']->id);

        return $data;
    }

    private function getChallanArrearsAndFine($generated_challan_Id): array|float
    {
        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }

        $ids = explode(',', $generated_challan_Id);
        $challan = GenerateFeeChallan::with('ChallanTransactions', 'StudentRel.class', 'StudentRel.section', 'ChallanArrearsRel')
            ->whereIn('id', $ids)
            ->where('tenant_id', $tenant_id)
            ->get();
        $challans = $challan->toArray();

        $previousChallanData = [];
        foreach ($challans as $challnList) {
            foreach ($challnList['challan_arrears_rel'] as $arrears) {
                $generateChallanFineList = GenerateFeeChallan::where('id', $arrears['FKeyId'])->where('tenant_id', $tenant_id)->first();
                if ($arrears['TransactionType'] == 'Arrears' && $generateChallanFineList->IsPartialPayment == 0) {
                    $ChallanTransactionsSum = ChallanTransactions::where('generate_challan_id', $arrears['FKeyId'])->sum('BalanceFeeAfterDiscount');
                    $ChallanTransactionsDueMonth = GenerateFeeChallan::select('id', 'tenant_id', 'challan_no', 'ChallanMonth', 'WaivedFineAmount', 'FineAmount')
                        ->where('tenant_id', $tenant_id)
                        ->where('id', $arrears['FKeyId'])->first();

                    $formatedMonth = Carbon::parse($ChallanTransactionsDueMonth['ChallanMonth'])->format('M-Y');
                    //  $dueDate = Carbon::createFromFormat('M-Y-d', $challnList['DueDate']);
                    //  $expiryDate = Carbon::createFromFormat('M-d-Y', $challnList['ExpiryDate']);
                    //  $days = $dueDate->diffInDays($expiryDate);

                    $dueDate = Carbon::parse($generateChallanFineList['DueDate']);
                    $expiryDate = Carbon::parse($generateChallanFineList['ExpiryDate']);
                    $days = $dueDate->diffInDays($expiryDate);

                    $totalFine = $days * $generateChallanFineList['per_day_fine'];
                    $previousChallanData[$challnList['id']][] = [
                        'total_amount' => $ChallanTransactionsSum,
                        'total_fine' => $generateChallanFineList['per_day_fine'] ? $totalFine : $generateChallanFineList['FineAmount'],
                        'ChallanMonth' => $formatedMonth,
                        'has_arraear' => 'no',
                    ];
                }
                if ($arrears['TransactionType'] == 'Arrears' && $generateChallanFineList->IsPartialPayment == 1) {
                    $ChallanTransactionsSum = ChallanTransactions::where('generate_challan_id', $arrears['FKeyId'])->sum('BalanceFeeAfterDiscount');
                    $ChallanTransactionsDueMonth = GenerateFeeChallan::select('id', 'tenant_id', 'challan_no', 'ChallanMonth', 'WaivedFineAmount', 'FineAmount')
                        ->where('tenant_id', $tenant_id)
                        ->where('id', $arrears['FKeyId'])->first();

                    $formatedMonth = Carbon::parse($ChallanTransactionsDueMonth['ChallanMonth'])->format('M-Y');

                    $ChallanPartialPaymentSum = ChallanPartialPayment::where('GenerateClassChallanId', $arrears['FKeyId'])->sum('ReceivedAmount');
                    $dueDate = Carbon::parse($generateChallanFineList['DueDate']);
                    $expiryDate = Carbon::parse($generateChallanFineList['ExpiryDate']);
                    $days = $dueDate->diffInDays($expiryDate);
                    $totalFine = $days * $generateChallanFineList['per_day_fine'];
                    $previousChallanData[$challnList['id']][] = [
                        'total_amount' => $ChallanTransactionsSum - $ChallanPartialPaymentSum,
                        'total_fine' => $generateChallanFineList['per_day_fine'] ? $totalFine : $generateChallanFineList['FineAmount'],
                        'ChallanMonth' => $formatedMonth,
                        'has_arraear' => 'yes',
                    ];
                }

                if ($arrears['TransactionType'] == 'Fine') {
                    $fineAmount = GenerateFeeChallan::select('id', 'tenant_id', 'challan_no', 'ChallanMonth', 'WaivedFineAmount', 'FineAmount')
                        ->where('tenant_id', $tenant_id)
                        ->where('id', $arrears['FKeyId'])->first();

                    $previousChallanData[$challnList['id']][] = [
                        'total_amount' => 0,
                        'total_fine' => $fineAmount->FineAmount,
                        'ChallanMonth' => $fineAmount->ChallanMonth->toDateString(),
                        'has_arraear' => 'no',
                    ];
                }

            }
        }
        $balance = collect($previousChallanData)->flatten(1)->sum('total_amount');
        $total_fine = collect($previousChallanData)->flatten(1)->sum('total_fine');

        return $total_remainings = $balance + $total_fine;

    }

    protected function updateChallanStatus($challanId, $request = null): void
    {
        $sum = ChallanPartialPayment::where('GenerateClassChallanId', $challanId)->sum('ReceivedAmount');
        $totalReceived = $sum + $request->waived_amount;
        $challan = GenerateFeeChallan::withSum('transection', 'BalanceFeeAfterDiscount')->findOrFail($challanId);
        $total_challan_amount = $this->getChallanArrearsAndFine($challanId);
        $final_challan_amount = $total_challan_amount + $challan->transection_sum_balancefeeafterdiscount;

        if ((int) $totalReceived >= $final_challan_amount) {
            $data = $this->getGenerateChallanData($request);
            $challan->update($data);
        } else {
            $challan->update([
                'IsPartialPayment' => false,
                'SubmitDate' => $request->SubmitDate,
                'WaivedFineAmount' => $request->waived_amount,
                'IsPartialPayment' => true,
                'ModifiedBy' => auth()->user()->id,
                'PaymentMode' => $request->PaymentMode,
                'Status' => 'Unpaid',
            ]);

        }

        if ($request->waived_amount > 0) {
            $challan->update(['WaivedFineAmount' => $request->waived_amount]);
        }
    }

    protected function updateGenerateFeeChallan($challanId, $data): void
    {
        $challan = GenerateFeeChallan::withSum('transection', 'BalanceFeeAfterDiscount')->findOrFail($challanId);
        $data = $this->getGenerateChallanData($data);
        $challan->update($data);
    }

    protected function getGenerateChallanData($request): array
    {
        return [
            'IsPartialPayment' => false,
            'ModifiedBy' => auth()->user()->id,
            // 'Note' => $request->note,
            'PaymentMode' => $request->PaymentMode,
            'CollectDate' => now(),
            'SubmitDate' => $request->SubmitDate,
            'WaivedFineAmount' => $request->waived_amount,
            'FineAmount' => $request->fine_amount,
            'Status' => 'Paid',
        ];
    }
}
