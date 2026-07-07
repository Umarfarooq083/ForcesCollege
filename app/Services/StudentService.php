<?php

namespace App\Services;

use App\Models\Campus;
use App\Models\ChallanPartialPayment;
use App\Models\ChallanTransactions;
use App\Models\GenerateFeeChallan;
use App\Models\GuardianInfo;
use App\Models\Student;
use App\Models\StudentDocuments;
use App\Models\StudentLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Fluent;

class StudentService
{
    public function index($request)
    {
        $students = Student::query();
        $students->with('class.sections', 'disabledReason')
            ->orderby('id', 'desc')
            ->where(function ($q) {
                $q->where('withdraw_status', '<>', 'Approved')
                    ->orWhereNull('withdraw_status');
            })
                    // ->where('IsActive', 1)
            ->where('tenant_id', tenant('id'));

        if ($request->filled('search')) {
            $search = $request->search;
            $students->where(function ($q) use ($search) {
                $q->where('FirstName', 'like', "%{$search}%")
                    ->orWhere('LastName', 'like', "%{$search}%")
                    ->orWhere('RollNumber', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(FirstName, ' ', LastName) LIKE ?", ["%{$search}%"])
                    ->orWhereHas('class', function ($sub) use ($search) {
                        $sub->where('ClassName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('class.sections', function ($sub) use ($search) {
                        $sub->where('SectionName', 'like', "%{$search}%");
                    });
            });
        }

        return $students->paginate(25)->withQueryString();
    }

    public function disablelist()
    {
        $disablestudents = DB::table('student_disable_log')
            ->where('tenant_id', tenant('id'))
            ->orderBy('id', 'desc')
            ->paginate(25);

        $disablestudents->getCollection()->transform(function ($item) {
            $item->created_at = \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');

            return $item;
        });

        return $disablestudents;
    }

    public function submit($request): void
    {
        // dd($request->all());
        $studentfilePath = $this->handleFileUpload($request, 'StudentPhotoPath', 'student_inquiry');
        $fatherfilePath = $this->handleFileUpload($request, 'FatherPhotoPath', 'student_inquiry');
        $motherfilePath = $this->handleFileUpload($request, 'MotherPhotoPath', 'student_inquiry');
        $guardianfilePath = $this->handleFileUpload($request, 'GuardianPhotoPath', 'student_inquiry');

        $validated = $request->validated();
        $validated['tenant_id'] = tenant('id');
        $validated['IsActive'] = 1;
        $validated['CreatedBy'] = Auth::id();
        $validated['AdmissionEnquiryId'] = $request->inquiryId;
        $validated['CreatedDate'] = now();
        $validated['Password'] = Hash::make('student@123');
        $validated['StudentPhotoPath'] = $studentfilePath;
        $validated['FatherCnic'] = $request->FatherCnicName;
        $validated['FatherPhotoPath'] = $fatherfilePath;
        $validated['MotherPhotoPath'] = $motherfilePath;
        $validated['GuardianPhotoPath'] = $guardianfilePath;
        $validated['RollNumber'] = $this->generateRollNo();

        $studentData = Student::create([
            ...$validated,
        ]);

        if ($studentData) {
            userActivityLogs('Student Created and By User ID: '.auth()->user()->id.'', StudentLog::class);
        }

        if (isset($request->rows) && count($request->rows) > 0) {
            $insertArray = [];
            foreach ($request->rows as $key => $files) {
                $filePath = $this->multipleFileUpload($files, 'document', 'student_documents');
                $insertArray[$key]['student_id'] = $studentData->id;
                $insertArray[$key]['tenant_id'] = tenant('id');
                $insertArray[$key]['Title'] = $files['Title'];
                $insertArray[$key]['document'] = $filePath;
                $insertArray[$key]['status'] = 1;
            }
            StudentDocuments::insert($insertArray);
        }
    }

    public function toggleStatus($request, $id): void
    {

        $student = Student::where('tenant_id', tenant('id'))->findOrFail($id);
        $student->IsActive = ! $student->IsActive;
        $student->IsDisable = ! $student->IsDisable;

        if (isset($request->Status) && $request->Status === 'disabled') {
            $student->DisableReasonId = $request->ReasonId;
        } else {
            $student->DisableReasonId = null;
        }

        if (isset($request->Status) && $request->Status === 'disabled') {
            $reason = $request->Reason['name'];
        } else {
            $reason = $request->Reason;
        }

        DB::table('student_disable_log')->insert([
            'student_id' => $student->id,
            'tenant_id' => $student->tenant_id,
            'CreatedBy' => Auth::id(),
            'FromDate' => $request->FromDate,
            'ToDate' => $request->ToDate,
            'Reason' => $reason,
            'Type' => $student->IsActive ? 'Enable' : 'Disable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($student->save()) {
            userActivityLogs('Student Status Updated and id is '.$id.' student id is '.$student->id.' and By User ID: '.auth()->user()->id.'', StudentLog::class);
        }
    }

    public function withdrawSubmit($request, $id): void
    {

        $student = Student::where('tenant_id', tenant('id'))->findOrFail($id);
        if ($student) {
            $student->withdraw_status = $request->Status;
            $student->withdraw_date = $request->FromDate;
            $student->withdraw_reason = $request->Reason['name'];
            $student->last_challan_no = $request->challan_no;
            $student->last_challan_amount = $request->pending_amount;
            $student->last_challan_status = $request->last_challan_status;
            $student->save();
        }

        DB::table('student_disable_log')->insert([
            'student_id' => $student->id,
            'tenant_id' => $student->tenant_id,
            'CreatedBy' => Auth::id(),
            'FromDate' => $request->FromDate,
            'Reason' => $request->Reason['name'],
            'Type' => 'Disable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($student->save()) {
            userActivityLogs('Student withdraw created or updated and id is '.$id.' student id is '.$student->id.' and By User ID: '.auth()->user()->id.'', StudentLog::class);
        }
    }

    public function getWithdrawList()
    {
        $withdrawingStudents = Student::with('class', 'section', 'disabledReason')
            ->where('tenant_id', tenant('id'))
            ->where('withdraw_status', '<>', null)
            ->orderBy('id', 'desc')
            ->paginate(25);

        return $withdrawingStudents;
    }

    public function approveWithdraw($request, $id): void
    {
        $student = Student::where('tenant_id', tenant('id'))->findOrFail($id);

        $student->withdraw_status = 'Approved';
        $student->IsDisable = 1;
        $student->IsActive = 0;
        $student->DisableReasonId = $student->DisableReasonId ?? null;
        $student->save();

        DB::table('student_disable_log')->insert([
            'student_id' => $student->id,
            'tenant_id' => $student->tenant_id,
            'CreatedBy' => Auth::id(),
            'FromDate' => $student->withdraw_date,
            'Reason' => $student->withdraw_reason,
            'Type' => 'Disable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        userActivityLogs('Student withdraw approved and id is '.$id.' student id is '.$student->id.' and By User ID: '.auth()->user()->id.'', StudentLog::class);
    }

    public function rejectWithdraw($request, $id): void
    {
        $student = Student::where('tenant_id', tenant('id'))->findOrFail($id);

        $student->withdraw_status = 'Rejected';
        $student->save();

        userActivityLogs('Student withdraw rejected and id is '.$id.' student id is '.$student->id.' and By User ID: '.auth()->user()->id.'', StudentLog::class);
    }

    public function getReAdmissionList()
    {
        $readmissionStudents = Student::with('class', 'section', 'disabledReason')
            ->where('tenant_id', tenant('id'))
            ->where('withdraw_status', 'Approved')
            // ->where('readmission_status', false)
            ->orderBy('id', 'desc')
            ->paginate(25);

        return $readmissionStudents;
    }

    public function readmission($request)
    {
        $student = Student::select(
            'id', 'tenant_id', 'RollNumber', 'FirstName', 'LastName',
            'ClassId', 'SectionId', 'FatherName', 'FatherPhone',
            'AdmissionDate', 'CurrentAddress', 'withdraw_date', 'withdraw_reason', 'readmitted_date', 'readmission_reason')
            ->with('class', 'section')
            ->where('tenant_id', tenant('id'))->findOrFail($request->id);

        return [
            'student' => $student,
        ];
    }

    public function submitReAdmission($request, $id): void
    {
        $student = Student::where('tenant_id', tenant('id'))->findOrFail($id);

        if ($student) {
            $student->readmitted_date = $request->readmitted_date;
            $student->readmission_status = true;
            $student->IsActive = 1;
            $student->IsDisable = 0;
            $student->last_challan_status = NULL;
            $student->last_challan_amount = NULL;
            $student->withdraw_date = NULL;
            $student->last_challan_no = NULL;
            $student->withdraw_status = NULL;
            $student->withdraw_reason = NULL;
            $student->save();
        }

        DB::table('student_disable_log')->insert([
            'student_id' => $student->id,
            'tenant_id' => $student->tenant_id,
            'CreatedBy' => Auth::id(),
            'FromDate' => $request->readmitted_date,
            'Reason' => 're-admition',
            'Type' => 'Enable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        userActivityLogs('Student readmission submitted and id is '.$id.' student id is '.$student->id.' and By User ID: '.auth()->user()->id.'', StudentLog::class);
    }

    private function handleFileUpload($request, string $fieldName, string $folderName): ?string
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $ext = $file->extension();

            $folder = $folderName.'/'.now()->year.'/'.now()->format('F');
            $fileName = uniqid().'_'.time().'.'.$ext;

            return $file->storeAs($folder, $fileName, 'public');
        }

        return null;
    }

    private function multipleFileUpload($request, string $fieldName, string $folderName): ?string
    {
        // Agar array aya hai to object-like access ke liye Fluent wrap kar do
        if (is_array($request)) {
            $request = new Fluent($request);
        }

        // Check file availability
        $file = null;

        if (method_exists($request, 'hasFile') && $request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
        } elseif (isset($request->{$fieldName}) && $request->{$fieldName} instanceof \Illuminate\Http\UploadedFile) {
            $file = $request->{$fieldName};
        }

        if (! $file) {
            return null;
        }

        $ext = $file->extension();
        $folder = $folderName.'/'.now()->year.'/'.now()->format('F');
        $fileName = uniqid().'_'.time().'.'.$ext;

        return $file->storeAs($folder, $fileName, 'public');
    }

    public function detail($request)
    {
        return Student::where('tenant_id', tenant('id'))->with('studentSession', 'guardianRel', 'class', 'section', 'studentDocuments')->findOrFail($request->id);
    }

    public function update($request): void
    {
        // dd($request->all());
        $GuardianInfoExist = GuardianInfo::where('tenant_id', tenant('id'))->where('cnic', $request->FatherCnicName)->first();
        // dd($GuardianInfoExist);
        if (! $GuardianInfoExist) {
            GuardianInfo::create([
                'tenant_id' => tenant('id'),
                'name' => $request->FatherName,
                'cnic' => $request->FatherCnicName,
            ]);
        }

        $validated = $request->validated();
        $validated['ModifiedBy'] = auth()->user()->id;
        $validated['ModifiedDate'] = now();

        // $student = Student::where('id', $request->id)->where('tenant_id',tenant('id'))->where('FatherCnic', $request->FatherCnicName)->first();
        $student = Student::where('id', $request->id)->where('tenant_id', tenant('id'))->first();
        $uploadedPath = $this->handleFileUpload($request, 'StudentPhotoPath', 'student_inquiry');
        if ($uploadedPath) {
            if ($student && $student->StudentPhotoPath && Storage::disk('public')->exists($student->StudentPhotoPath)) {
                Storage::disk('public')->delete($student->StudentPhotoPath);
            }

            $validated['StudentPhotoPath'] = $uploadedPath;
        } else {
            $validated['StudentPhotoPath'] = $student->StudentPhotoPath ?? null;
        }

        $updated = $student->update([
            ...$validated,
            'FatherCnic' => $request->FatherCnicName,
        ]);

        if ($updated) {
            userActivityLogs('Student Updated and student id is '.$updated->id.' By User ID: '.auth()->user()->id.'', StudentLog::class);
        }
    }

    private function generateRollNo()
    {

        $campus = Campus::where('tenant_id', tenant('id'))->first();
        if (! $campus) {
            throw new \Exception('Campus not found for tenant: '.tenant('id'));
        }
        $campusDomain = $campus->DomainName;
        // Find last student roll number for this campus-domain
        $lastStudent = Student::where('tenant_id', tenant('id'))
            ->where('RollNumber', 'LIKE', 'FSS-'.$campusDomain.'-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->RollNumber, -5));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $rollSuffix = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return 'FSS-'.$campusDomain.'-'.$rollSuffix;
    }

    public function withdraw($request)
    {
        $student = Student::select(
            'id', 'tenant_id', 'RollNumber', 'FirstName', 'LastName',
            'ClassId', 'SectionId', 'FatherName', 'FatherPhone',
            'AdmissionDate', 'CurrentAddress')
            ->with('class', 'section')
            ->where('tenant_id', tenant('id'))->findOrFail($request->id);

        $lastChallan = GenerateFeeChallan::with('ChallanTransactions', 'ChallanArrearsRel')
            ->where('StudentId', $student->id)
            ->where('tenant_id', tenant('id'))
            ->orderBy('id', 'desc')
            ->first();

        $lastChallanData = [
            'status' => '',
            'challan_no' => null,
            'pending_amount' => 0,
            'arrears' => [],
            'total_arrears_amount' => 0,
            'total_fine_amount' => 0,
            'waived_amount' => 0,
            'partial_payments' => [],
        ];

        if ($lastChallan) {
            $lastChallanData['status'] = $lastChallan->Status;
            $lastChallanData['challan_no'] = $lastChallan->challan_no;
            $lastChallanData['DueDate'] = $lastChallan->DueDate;
            $lastChallanData['ExpiryDate'] = $lastChallan->ExpiryDate;
            $lastChallanData['WaivedFineAmount'] = $lastChallan->WaivedFineAmount ?? 0;

            $challanTransactionsSum = ChallanTransactions::where('generate_challan_id', $lastChallan->id)->sum('BalanceFeeAfterDiscount');

            foreach ($lastChallan->ChallanArrearsRel as $arrears) {
                $arrearsChallan = GenerateFeeChallan::where('id', $arrears->FKeyId)->first();
                $totalArrearsAmount = 0;
                $totalFineAmount = 0;

                if ($arrears->TransactionType == 'Arrears' && $arrearsChallan) {
                    $arrearsChallanTransactionsSum = ChallanTransactions::where('generate_challan_id', $arrears->FKeyId)->sum('BalanceFeeAfterDiscount');

                    if ($arrearsChallan->IsPartialPayment == 1) {
                        $partialPaymentSum = ChallanPartialPayment::where('GenerateClassChallanId', $arrears->FKeyId)->sum('ReceivedAmount');
                        $totalArrearsAmount = $arrearsChallanTransactionsSum - $partialPaymentSum;
                    } else {
                        $totalArrearsAmount = $arrearsChallanTransactionsSum;
                    }

                    $dueDate = Carbon::parse($arrearsChallan->DueDate);
                    $expiryDate = Carbon::parse($arrearsChallan->ExpiryDate);
                    $days = $dueDate->diffInDays($expiryDate);
                    $totalFineAmount = $arrearsChallan->per_day_fine ? ($days * $arrearsChallan->per_day_fine) : $arrearsChallan->FineAmount;

                    $waivedAmount = $arrearsChallan->WaivedFineAmount ?? 0;

                    $lastChallanData['arrears'][] = [
                        'challan_id' => $arrears->FKeyId,
                        'challan_no' => $arrearsChallan->challan_no,
                        'ChallanMonth' => $arrearsChallan->ChallanMonth ? Carbon::parse($arrearsChallan->ChallanMonth)->format('M-Y') : null,
                        'total_amount' => $totalArrearsAmount,
                        'total_fine' => $totalFineAmount,
                        'waived' => $waivedAmount,
                        'is_partial_payment' => $arrearsChallan->IsPartialPayment,
                    ];

                    $lastChallanData['total_arrears_amount'] += $totalArrearsAmount;
                    $lastChallanData['total_fine_amount'] += $totalFineAmount;
                    $lastChallanData['waived_amount'] += $waivedAmount;

                    if ($arrearsChallan->IsPartialPayment == 1) {
                        $lastChallanData['partial_payments'][] = [
                            'challan_id' => $arrears->FKeyId,
                            'challan_no' => $arrearsChallan->challan_no,
                            'partial_amount_paid' => $partialPaymentSum ?? 0,
                        ];
                    }
                }

                if ($arrears->TransactionType == 'Fine' && $arrearsChallan) {
                    $lastChallanData['arrears'][] = [
                        'challan_id' => $arrears->FKeyId,
                        'challan_no' => $arrearsChallan->challan_no,
                        'ChallanMonth' => $arrearsChallan->ChallanMonth ? Carbon::parse($arrearsChallan->ChallanMonth)->format('M-Y') : null,
                        'total_amount' => 0,
                        'total_fine' => $arrearsChallan->FineAmount ?? 0,
                        'waived' => 0,
                        'is_fine' => true,
                    ];

                    $lastChallanData['total_fine_amount'] += $arrearsChallan->FineAmount ?? 0;
                }
            }

            $lastChallanData['pending_amount'] = $challanTransactionsSum + $lastChallanData['total_arrears_amount'] + $lastChallanData['total_fine_amount'] - $lastChallanData['waived_amount'];
        }

        return [
            'student' => $student,
            'lastChallan' => $lastChallanData,
        ];
    }
}
