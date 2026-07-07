<?php

namespace App\Http\Controllers\APIImportData;

use App\Models\Api_imported_models\ImportedSessions;
use App\Models\ArrearChallanDetails;
use App\Models\CampusFeesMaster;
use App\Models\ChallanPartialPayment;
use App\Models\ChallanTransactions;
use App\Models\Classes;
use App\Models\ExamGrade;
use App\Models\ExamMarks;
use App\Models\ExamMarksDetail;
use App\Models\ExamSubject;
use App\Models\ExamTerm;
use App\Models\ExamType;
use App\Models\FeesType;
use App\Models\GenerateFeeChallan;
use App\Models\OptionalFeeMaster;
use App\Models\Student;
use App\Models\StudentFeeDiscount;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class ImportFeeStracture
{
    protected $ImportHelper;
    public function __construct(ImportHelper $ImportHelper)
    {
        $this->ImportHelper = $ImportHelper;
    }

    public function handleFeeStracture($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();

        // dd($current_tenant_id);
        // ImportedFeeType::truncate();
        // ImportedCampusFeeMaster::truncate();
        // foreach($finalResponse as $feetype)
        // {
        //     // ImportedFeeType::create([
        //     //     'feesCode' => $feetype['feesCode'],
        //     //     'imported_fee_type_id' => $feetype['id'],
        //     //     'feeName' => $feetype['feeName'],
        //     //     'description' => $feetype['description'],
        //     //     'feeCycle' => $feetype['feeCycle'],
        //     //     'applicableMonth' => $feetype['applicableMonth'],
        //     //     'isOptional' => $feetype['isOptional'],
        //     //     'isRefundable' => $feetype['isRefundable'],
        //     //     'campusFeesMastersExist' => (count($feetype['campusFeesMasters']) == 0) ? 0 : 1,
        //     //     'isActive' => $feetype['isActive'],
        //     //     'isDeleted' => $feetype['isDeleted'],
        //     //     'createdBy' => $feetype['createdBy'],
        //     //     'createdDate' => $feetype['createdDate'],
        //     // ]);

        //     if(count($feetype['campusFeesMasters']) > 0 ){
        //         foreach($feetype['campusFeesMasters'] as $campusMaster)
        //         {
        //             // ImportedCampusFeeMaster::create([
        //             //     'title' => $campusMaster['title'],
        //             //     'feesTypeNId' => $campusMaster['feesTypeNId'],
        //             //     'amount' => $campusMaster['amount'],
        //             //     'sectionId' => $campusMaster['sectionId'],
        //             //     'sessionId' => $campusMaster['sessionId'],
        //             //     'classId' => $campusMaster['classId'],
        //             //     'imported_fee_master_id' => $campusMaster['id'],
        //             //     'isActive' => ($campusMaster['isActive'] == true) ? 1 : 0,
        //             //     'isDeleted' => $campusMaster['isDeleted'],
        //             //     'createdBy' => $campusMaster['createdBy'],
        //             //     'createdDate' => $campusMaster['createdDate'],
        //             // ]);     
        //         }

        //     }
        // }

        // FeesType::truncate();
        // CampusFeesMaster::truncate();
       

         FeesType::query()->update(['import_fee_type_id' => null]);

        foreach ($finalResponse as $createStracture) {
            $existFeesType = FeesType::withTrashed()
            ->where('IsActive',$createStracture['isActive'])
            ->where('FeeCycle',$createStracture['feeCycle'])
            ->where('ApplicableMonth',$createStracture['applicableMonth'])
            ->where('FeesCode', $createStracture['feesCode'])
            ->where('FeeName', $createStracture['feeName'])
            ->first();

            if (!$existFeesType) {
                $FeesTypeInsert = FeesType::create([
                    'FeesCode' => $createStracture['feesCode'],
                    'FeeName' => $createStracture['feeName'],
                    'Description' => $createStracture['description'],
                    'FeeCycle' => $createStracture['feeCycle'],
                    'ApplicableMonth' => $createStracture['applicableMonth'],
                    'IsOptional' => $createStracture['isOptional'],
                    'IsRefundable' => $createStracture['isRefundable'],
                    'import_fee_type_id' => $createStracture['id'],
                    'CreatedBy' => $createStracture['createdBy'],
                    'created_at' => $createStracture['createdDate'],
                    'IsActive' => ($createStracture['isActive'] === true) ? '1' : '0',
                    'deleted_at' => ($createStracture['isDeleted'] ===  true) ? now() : NULL,
                ]);
            }else{
                $existFeesType->update([
                    'import_fee_type_id' => $createStracture['id']
                ]);
            }
        }

        foreach ($finalResponse as $createStracture) {
            $existFeesType = FeesType::withTrashed()
            ->where('IsActive',$createStracture['isActive'])
            ->where('FeeCycle',$createStracture['feeCycle'])
            ->where('ApplicableMonth',$createStracture['applicableMonth'])
            ->where('FeesCode', $createStracture['feesCode'])
            ->where('FeeName', $createStracture['feeName'])
            ->first();

            if ($existFeesType) {
                if (count($createStracture['campusFeesMasters']) > 0) {
                    foreach ($createStracture['campusFeesMasters'] as $index => $campusMasterFinal) {
                        $currentSessionId = $this->ImportHelper->getSessionId($campusMasterFinal);
                        $internal_class_id = $this->ImportHelper->getClassId($campusMasterFinal);
                        $isDeleted = ($campusMasterFinal['isDeleted'] === true) ? now()->toDateTimeString() : NULL;
                        // dd($isDeleted);
                        $recordExist = CampusFeesMaster::withTrashed()->where('tenant_id', $current_tenant_id)
                            ->where('import_fee_master_id', $campusMasterFinal['id'])
                            ->first();
                  
                        if (!$recordExist) {
                           
                            $existCampusFeesMaster = CampusFeesMaster::where('tenant_id', $current_tenant_id)
                                ->where('SessionId', $currentSessionId)
                                ->where('ClassId', $internal_class_id)
                                ->where('import_fee_type_id', $campusMasterFinal['feesTypeNId'])
                                ->where('IsActive', true)
                                ->first();

                            $isActive = ($campusMasterFinal['isActive'] == true) ? 'true' : 'false';
                            if ($existCampusFeesMaster) {
                                $FeesTypeInsert = FeesType::create([
                                    'FeesCode' => $createStracture['feesCode'],
                                    'FeeName' => $createStracture['feeName'] . ' - ' . ($index + 1),
                                    'Description' => $createStracture['description'],
                                    'FeeCycle' => $createStracture['feeCycle'],
                                    'ApplicableMonth' => $createStracture['applicableMonth'],
                                    'IsOptional' => $createStracture['isOptional'] ? 1 : 0,
                                    'IsRefundable' => $createStracture['isRefundable'] ? 1 : 0,
                                    'import_fee_type_id' => $createStracture['id'],
                                    'CreatedBy' => $createStracture['createdBy'],
                                    'created_at' => $createStracture['createdDate'],
                                    'IsActive' => $createStracture['isActive'] ? 1 : 0,
                                    'deleted_at' => $createStracture['isDeleted'] ? now() : null,
                                ]);

                                CampusFeesMaster::create([
                                    'tenant_id' => $current_tenant_id,
                                    'CreatedBy' => $campusMasterFinal['createdBy'],
                                    'Title' => $campusMasterFinal['title'],
                                    'FeesTypeNId' => $FeesTypeInsert->id,
                                    'Amount' => $campusMasterFinal['amount'],
                                    'SessionId' => $currentSessionId,
                                    'ClassId' => $internal_class_id,
                                    'deleted_at' =>  $isDeleted,
                                    'import_fee_type_id' => $campusMasterFinal['feesTypeNId'],
                                    'import_fee_master_id' => $campusMasterFinal['id'],
                                    'IsActive' => $isActive,
                                ]);
                            } else {

                                CampusFeesMaster::create([
                                    'tenant_id' => $current_tenant_id,
                                    'CreatedBy' => $campusMasterFinal['createdBy'],
                                    'Title' => $campusMasterFinal['title'],
                                    'FeesTypeNId' => $existFeesType->id, // Use existing FeesType ID
                                    'Amount' => $campusMasterFinal['amount'],
                                    'SessionId' => $currentSessionId,
                                    'ClassId' => $internal_class_id,
                                    'deleted_at' =>  $isDeleted,
                                    'import_fee_type_id' => $campusMasterFinal['feesTypeNId'],
                                    'import_fee_master_id' => $campusMasterFinal['id'],
                                    'IsActive' => $isActive,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function handleFeeMapping($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insert_fee_mapping = [];
        foreach($finalResponse as $key => $optionalFeeMapping)
        {
            $currentSessionId = $this->ImportHelper->getSessionId($optionalFeeMapping);
            $internal_student_data = $this->ImportHelper->getStudentId($optionalFeeMapping);
            // \Log::info($optionalFeeMapping);
            $internal_fee_type_id = $this->ImportHelper->getFeeTypeId($optionalFeeMapping);
            $internal_campus_fee_master_id = $this->ImportHelper->getCampusFeeMasterId($optionalFeeMapping);
            $existingRecord = OptionalFeeMaster::withTrashed()->where('tenant_id',$current_tenant_id)->where('imported_fee_mapping_id', $optionalFeeMapping['id'])->first();
            if ($existingRecord) {
                continue; 
            }
            $insert_fee_mapping[$key]['tenant_id'] = $current_tenant_id; 
            $insert_fee_mapping[$key]['FeesTypeNId'] = $internal_fee_type_id; 
            $insert_fee_mapping[$key]['ClassId'] = $internal_student_data->ClassId; 
            $insert_fee_mapping[$key]['SectionId'] = $internal_student_data->SectionId; 
            $insert_fee_mapping[$key]['StudentId'] = $internal_student_data->id; 
            $insert_fee_mapping[$key]['FromMonth'] = $optionalFeeMapping['fromMonth']; 
            $insert_fee_mapping[$key]['ToMonth'] = $optionalFeeMapping['toMonth']; 
            $insert_fee_mapping[$key]['CampusFeesMasterId'] = $internal_campus_fee_master_id; 
            $insert_fee_mapping[$key]['IsActive'] = ($optionalFeeMapping['isActive'] === false) ? 0 : 1; 
            $insert_fee_mapping[$key]['CreatedBy'] = $optionalFeeMapping['createdBy']; 
            $insert_fee_mapping[$key]['SessionId'] = $currentSessionId; 
            $insert_fee_mapping[$key]['deleted_at'] = ($optionalFeeMapping['isDeleted'] ===  true) ? now() : NULL; 
            $insert_fee_mapping[$key]['imported_fee_mapping_id'] = $optionalFeeMapping['id']; 
        }
        OptionalFeeMaster::insert($insert_fee_mapping); 
    }

    public function handleStudentFeeDiscount($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insertDiscount = [];
        foreach($finalResponse as $key=> $studentFeeDiscount)
        {
            $existingRecord = StudentFeeDiscount::where('tenant_id',$current_tenant_id)->where('imported_student_fee_discount_id', $studentFeeDiscount['id'])->first();
            if ($existingRecord) {
                continue; 
            }
            $currentSessionId = $this->ImportHelper->getSessionId($studentFeeDiscount); 
            $internal_student_data = $this->ImportHelper->getStudentId($studentFeeDiscount);
            $internal_campus_fee_master_id = $this->ImportHelper->getCampusFeeMasterId($studentFeeDiscount);
           
            $insertDiscount[$key]['tenant_id'] = $current_tenant_id; 
            $insertDiscount[$key]['IsActive'] = ($studentFeeDiscount['isActive'] === false) ? 0 : 1; ; 
            $insertDiscount[$key]['CreatedBy'] = $studentFeeDiscount['createdBy']; 
            $insertDiscount[$key]['SessionId'] = $currentSessionId; 
            $insertDiscount[$key]['StudentId'] = $internal_student_data->id; 
            $insertDiscount[$key]['CampusFeesMasterId'] = $internal_campus_fee_master_id; 
            $insertDiscount[$key]['DiscountAmount'] = $studentFeeDiscount['discountAmount']; 
            $insertDiscount[$key]['BalanceFeeAfterDiscount'] = $studentFeeDiscount['balanceFeeAfterDiscount'];
            $insertDiscount[$key]['DiscountType'] = $studentFeeDiscount['discountType'];
            $insertDiscount[$key]['TotalFee'] = $studentFeeDiscount['totalFee'];
            $insertDiscount[$key]['deleted_at'] = ($studentFeeDiscount['isDeleted'] ===  true) ? now() : NULL;
            $insertDiscount[$key]['loadedCampusMaster'] = 1;
            $insertDiscount[$key]['imported_student_fee_discount_id'] = $studentFeeDiscount['id'];
        }
        StudentFeeDiscount::insert($insertDiscount);
    }   

    public function handleStudentFeeChallan($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $tenantId = $this->ImportHelper->getTenantId();
        $existingRecords = GenerateFeeChallan::withTrashed()
            ->where('tenant_id', $tenantId)
            ->pluck('imported_fee_challan_id')
            ->toArray();

        $existingLookup = array_flip($existingRecords);
        $sessionIds = collect($finalResponse)->pluck('sessionId')->unique();
        $studentIds = collect($finalResponse)->pluck('studentId')->unique();

        $sessionsMap = ImportedSessions::whereIn('imported_session_id', $sessionIds)
            ->pluck('lms_session_id', 'imported_session_id');

        $studentsMap = Student::withTrashed()
            ->where('tenant_id', $tenantId)
            ->whereIn('imported_student_id', $studentIds)
            ->pluck('id', 'imported_student_id');

        $insertFeeChallan = [];

        foreach ($finalResponse as $feeChallan) {

            if (isset($existingLookup[$feeChallan['id']])) {
                continue;
            }

            if (
                !isset($sessionsMap[$feeChallan['sessionId']]) ||
                !isset($studentsMap[$feeChallan['studentId']])
            ) {
                continue;
            }

            $insertFeeChallan[] = [
                'tenant_id'               => $tenantId,
                'IsActive'                => $feeChallan['isActive'],
                'CreatedBy'               => $feeChallan['createdBy'],
                'SessionId'               => $sessionsMap[$feeChallan['sessionId']],
                'StudentId'               => $studentsMap[$feeChallan['studentId']],
                'ChallanMonth'            => $feeChallan['challanMonth'],
                'DueDate'                 => $feeChallan['dueDate'],
                'ExpiryDate'              => $feeChallan['expiryDate'],
                'Status'                  => $feeChallan['status'],
                'SubmitDate'              => $feeChallan['submitDate'],
                'PaymentMode'             => $feeChallan['paymentMode'],
                'FineAmount'              => $feeChallan['fineAmount'] ?? 0,
                'WaivedFineAmount'        => $feeChallan['waivedFineAmount'] ?? 0,
                'Note'                    => $feeChallan['note'],
                'CollectDate'             => $feeChallan['collectDate'],
                'CollectBy'               => $feeChallan['collectBy'],
                'IsPartialPayment'        => $feeChallan['isPartialPayment'],
                'deleted_at'              => $feeChallan['isDeleted'] ? now() : null,
                'imported_fee_challan_id' => $feeChallan['id'],
                'challan_no'              => $feeChallan['id'],
            ];
        }

        foreach (array_chunk($insertFeeChallan, 500) as $chunk) {
            DB::table('generate_fee_challan')->insert($chunk);
        }
    }


    public function handleStudentFeeChallanTranscation($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $tenantId = $this->ImportHelper->getTenantId();
        $existingRecords = ChallanTransactions::withTrashed()
            ->where('tenant_id', $tenantId)
            ->pluck('imported_challan_transcation_id')
            ->toArray();

        $existingLookup = array_flip($existingRecords);
        $sessionIds = collect($finalResponse)->pluck('sessionId')->unique();
        $generateChallanIds = collect($finalResponse)->pluck('generateClassChallanId')->unique();
        $sessionsMap = ImportedSessions::whereIn('imported_session_id', $sessionIds)
            ->pluck('lms_session_id', 'imported_session_id');

        $generatedChallanMap = GenerateFeeChallan::withTrashed()
            ->where('tenant_id', $tenantId)
            ->whereIn('imported_fee_challan_id', $generateChallanIds)
            ->pluck('id', 'imported_fee_challan_id');

        $insertChallanTransaction = [];

        foreach ($finalResponse as $challanTranscation) {

            if (isset($existingLookup[$challanTranscation['id']])) {
                continue;
            }
            if (
                !isset($sessionsMap[$challanTranscation['sessionId']]) ||
                !isset($generatedChallanMap[$challanTranscation['generateClassChallanId']])
            ) {
                continue;
            }

            $feeAmount = $challanTranscation['feeAmount'];

            $insertChallanTransaction[] = [
                'tenant_id'                         => $tenantId,
                'generate_challan_id'               => $generatedChallanMap[$challanTranscation['generateClassChallanId']],
                'IsActive'                          => $challanTranscation['isActive'],
                'CreatedBy'                         => $challanTranscation['createdBy'],
                'SessionId'                         => $sessionsMap[$challanTranscation['sessionId']],
                'FKey'                              => $challanTranscation['fKey'],
                'Title'                             => $challanTranscation['title'],
                'FeeAmount'                         => $feeAmount,
                'TransactionType'                   => $challanTranscation['title'],
                'BalanceFeeAfterDiscount'           => $feeAmount,
                'TotalFee'                          => $feeAmount,
                'deleted_at'                        => $challanTranscation['isDeleted'] ? now() : null,
                'imported_challan_transcation_id'   => $challanTranscation['id'],
            ];
        }

        foreach (array_chunk($insertChallanTransaction, 500) as $chunk) {
            DB::table('challan_transactions')->insert($chunk);
        }
    }


    public function handleStudentChallanPartialPayment($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $insertchallanPartialPayments = [];
        $current_tenant_id = $this->ImportHelper->getTenantId();
        foreach($finalResponse as $key => $challanPartialPayment)
        {

            $existingRecord = ChallanPartialPayment::withTrashed()->where('tenant_id',$current_tenant_id)->where('imported_challan_partial_payment_id', $challanPartialPayment['id'])->first();
            if ($existingRecord) {
                continue; 
            }

            $currentGeneratedCallanId = $this->ImportHelper->getGenerateFeeChallanId($challanPartialPayment);
            $currentSessionId = $this->ImportHelper->getSessionId($challanPartialPayment);

            $insertchallanPartialPayments[$key]['tenant_id'] = $current_tenant_id; 
            $insertchallanPartialPayments[$key]['IsActive'] = $challanPartialPayment['isActive']; 
            $insertchallanPartialPayments[$key]['CreatedBy'] = $challanPartialPayment['createdBy']; 
            $insertchallanPartialPayments[$key]['SessionId'] = $currentSessionId; 
            $insertchallanPartialPayments[$key]['GenerateClassChallanId'] = $currentGeneratedCallanId; 
            $insertchallanPartialPayments[$key]['ReceivedAmount'] = $challanPartialPayment['receivedAmount']; 
            $insertchallanPartialPayments[$key]['CollectDate'] = $challanPartialPayment['collectDate']; 
            $insertchallanPartialPayments[$key]['CollectBy'] = $challanPartialPayment['collectBy']; 
            $insertchallanPartialPayments[$key]['PaymentMode'] = $challanPartialPayment['paymentMode']; 
            $insertchallanPartialPayments[$key]['SubmitDate'] = $challanPartialPayment['submitDate']; 
            $insertchallanPartialPayments[$key]['deleted_at'] = ($challanPartialPayment['isDeleted'] ===  true) ? now() : NULL;  
            $insertchallanPartialPayments[$key]['imported_challan_partial_payment_id'] = $challanPartialPayment['id']; 
        }
        DB::table('challan_partial_payments')->insert($insertchallanPartialPayments);
    }
    
    public function handleStudentChallanArrears($finalResponse)
    {
        ini_set('max_execution_time', 0);

        $tenantId = $this->ImportHelper->getTenantId();
        $existingRecords = ArrearChallanDetails::withTrashed()
            ->where('tenant_id', $tenantId)
            ->pluck('imported_challan_arrear_id')
            ->toArray();

        $existingLookup = array_flip($existingRecords);
        $sessionIds = collect($finalResponse)->pluck('sessionId')->unique();
        $generateChallanIds = collect($finalResponse)
            ->pluck('generateClassChallanId')
            ->merge(collect($finalResponse)->pluck('fKeyId'))
            ->unique();

        $sessionsMap = ImportedSessions::whereIn('imported_session_id', $sessionIds)
            ->pluck('lms_session_id', 'imported_session_id');

        // 4️⃣ Preload generate fee challans (single query)
        $generateChallanMap = GenerateFeeChallan::withTrashed()
            ->where('tenant_id', $tenantId)
            ->whereIn('imported_fee_challan_id', $generateChallanIds)
            ->get()
            ->keyBy('imported_fee_challan_id');

        $insertArrears = [];

        foreach ($finalResponse as $challanArrears) {

            if (isset($existingLookup[$challanArrears['id']])) {
                continue;
            }

            // Safety checks
            if (
                !isset($sessionsMap[$challanArrears['sessionId']]) ||
                !isset($generateChallanMap[$challanArrears['generateClassChallanId']]) ||
                !isset($generateChallanMap[$challanArrears['fKeyId']])
            ) {
                continue;
            }

            $mainChallan = $generateChallanMap[$challanArrears['generateClassChallanId']];
            $fKeyChallan = $generateChallanMap[$challanArrears['fKeyId']];

            $insertArrears[] = [
                'tenant_id'                   => $tenantId,
                'IsActive'                    => $challanArrears['isActive'],
                'CreatedBy'                   => $challanArrears['createdBy'],
                'SessionId'                   => $sessionsMap[$challanArrears['sessionId']],
                'GenerateFeeChallanId'        => $mainChallan->id,
                'FKeyId'                      => $fKeyChallan->id,
                'TransactionType'             => $challanArrears['transactionType'],
                'is_partial_payment'          => $fKeyChallan->IsPartialPayment ?? 0,
                'deleted_at'                  => $challanArrears['isDeleted'] ? now() : null,
                'imported_challan_arrear_id'  => $challanArrears['id'],
            ];
        }

        // 5️⃣ Chunk insert
        foreach (array_chunk($insertArrears, 500) as $chunk) {
            DB::table('arrears_challan_details')->insert($chunk);
        }
    }

   
    public function handleExamTerms($finalResponse)
    {
        ini_set('max_execution_time', 0);
        foreach($finalResponse as $examTerms)
        {
            $existingRecord = ExamTerm::withTrashed()
                ->where('ExamTermName', $examTerms['examTermName'])
                ->where('imported_exam_term_id', $examTerms['id'])
                ->first();
                
            if ($existingRecord) {
                $existingRecord->update([
                    'imported_exam_term_id'=> $examTerms['id']
                ]);
            }else{
                $insertExamTerm = new ExamTerm();
                $currentSessionId = $this->ImportHelper->getSessionId($examTerms);
                $insertExamTerm->IsActive = $examTerms['isActive'];
                $insertExamTerm->CreatedBy = $examTerms['createdBy'];
                $insertExamTerm->SessionId = $currentSessionId;
                $insertExamTerm->ExamTermName = $examTerms['examTermName'];
                $insertExamTerm->deleted_at = ($examTerms['isDeleted'] ===  true) ? now() : NULL;
                $insertExamTerm->imported_exam_term_id = $examTerms['id'];
                $insertExamTerm->save();
            }   
        }
    }
    
    public function handleExamTypes($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $insert_exam_type = [];
        $current_tenant_id = $this->ImportHelper->getTenantId();
        foreach($finalResponse as $key => $examTypes)
        {
            $existingRecord = ExamType::withTrashed()->where('tenant_id',$current_tenant_id)->where('imported_exam_id', $examTypes['id'])->first();
            if ($existingRecord) {
                continue; 
            }
            $currentSessionId = $this->ImportHelper->getSessionId($examTypes);
            $currentExamtermId = $this->ImportHelper->getExamTermId($examTypes);
            $insert_exam_type[$key]['tenant_id'] = $current_tenant_id;
            $insert_exam_type[$key]['IsActive'] = $examTypes['isActive'];
            $insert_exam_type[$key]['CreatedBy'] = $examTypes['createdBy'];
            $insert_exam_type[$key]['ExamName'] = $examTypes['examName'];
            $insert_exam_type[$key]['SessionId'] = $currentSessionId;
            $insert_exam_type[$key]['ExamTermId'] = $currentExamtermId;
            $insert_exam_type[$key]['ResultDeclarationDate'] = $examTypes['resultDeclarationDate'];
            $insert_exam_type[$key]['IsPublishResult'] = $examTypes['isPublishResult'];
            $insert_exam_type[$key]['Description'] = $examTypes['description'];
            $insert_exam_type[$key]['deleted_at'] = ($examTypes['isDeleted'] ===  true) ? now() : NULL;
            $insert_exam_type[$key]['imported_exam_id'] = $examTypes['id'];

        }
        ExamType::insert($insert_exam_type);
    }
    
    public function handleExamSubject($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insert_exam_subject = [];
        foreach($finalResponse as $key => $examSubjec)
        {
            $existingRecord = ExamSubject::withTrashed()->where('tenant_id',$current_tenant_id)->where('imported_exam_subject_id', $examSubjec['id'])->first();
            if ($existingRecord) {
                continue; 
            }

            $currentSessionId = $this->ImportHelper->getSessionId($examSubjec);
            $currentExamTypeId = $this->ImportHelper->getExamTypeId($examSubjec);
            $currentExamSubjectId = $this->ImportHelper->getExamSubjectId($examSubjec);
            if($currentExamSubjectId == null){
                continue; 
            }
            $internal_class_id = Subject::where('id',$currentExamSubjectId)->where('tenant_id',$current_tenant_id)->first();
            
            $insert_exam_subject[$key]['tenant_id'] = $current_tenant_id; 
            $insert_exam_subject[$key]['SessionId'] = $currentSessionId; 
            $insert_exam_subject[$key]['ExamId'] = $currentExamTypeId;
            $insert_exam_subject[$key]['SubjectId'] = $currentExamSubjectId; 
            $insert_exam_subject[$key]['ClassId'] = $internal_class_id->ClassId; 

            $insert_exam_subject[$key]['IsActive'] = $examSubjec['isActive']; 
            $insert_exam_subject[$key]['CreatedBy'] = $examSubjec['createdBy']; 
            $insert_exam_subject[$key]['Title'] = $examSubjec['title']; 
            $insert_exam_subject[$key]['Date'] = $examSubjec['date']; 

            $timeOnly = date('H:i:s', strtotime($examSubjec['time']));

            $insert_exam_subject[$key]['Time'] = $timeOnly; 
            $insert_exam_subject[$key]['Duration'] = $examSubjec['duration']; 
            $insert_exam_subject[$key]['CreditHours'] = $examSubjec['creditHours']; 
            $insert_exam_subject[$key]['MarksMax'] = $examSubjec['marksMax']; 
            $insert_exam_subject[$key]['MarksMin'] = $examSubjec['marksMin']; 
            $insert_exam_subject[$key]['deleted_at'] = ($examSubjec['isDeleted'] ===  true) ? now() : NULL; 
            $insert_exam_subject[$key]['imported_exam_subject_id'] = $examSubjec['id']; 
           
        }
        ExamSubject::insert($insert_exam_subject);
        
    }

    public function handleExamStudent($finalResponse)
    {
        dd($finalResponse);
    }

    public function handleExamMarks($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insert_exam_marks = [];
        foreach($finalResponse as $key => $examMarks)
        {
            $existingRecord = ExamMarks::withTrashed()->where('tenant_id',$current_tenant_id)->where('imported_exam_marks_id', $examMarks['id'])->first();
            if ($existingRecord) {
                continue; 
            }
            $currentSessionId = $this->ImportHelper->getSessionId($examMarks);
            $currentExamSubjectId = $this->ImportHelper->getExamSubjectDataId($examMarks);
            $insert_exam_marks[$key]['tenant_id'] = $current_tenant_id;
            $insert_exam_marks[$key]['IsActive'] = $examMarks['isActive'];
            $insert_exam_marks[$key]['CreatedBy'] = $examMarks['createdBy'];
            $insert_exam_marks[$key]['SessionId'] = $currentSessionId;
            $insert_exam_marks[$key]['ExamSubjectId'] = $currentExamSubjectId;
            $insert_exam_marks[$key]['deleted_at'] = ($examMarks['isDeleted'] ===  true) ? now() : NULL;
            $insert_exam_marks[$key]['imported_exam_marks_id'] = $examMarks['id'];
        }
        ExamMarks::insert($insert_exam_marks);
    }
    
    public function handleExamMarksDetail($finalResponse)
    {
        ini_set('memory_limit', '512M');
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $existingRecord = ExamMarksDetail::withTrashed()
        ->where('tenant_id', $current_tenant_id)
        ->pluck('imported_exam_detail_id')
        ->toArray();

        $insertExamMarks = [];
        foreach($finalResponse as $key => $examMarksDetail)
        {
            if (in_array($examMarksDetail['id'], $existingRecord)) {
                continue;
            }
            $internal_student_data = $this->ImportHelper->getStudentId($examMarksDetail);
            $insertExamMarks[$key]['tenant_id'] = $current_tenant_id;
            $insertExamMarks[$key]['IsActive'] = $examMarksDetail['isActive'];
            $insertExamMarks[$key]['CreatedBy'] = $examMarksDetail['createdBy'];
            $ExamMarksId = ExamMarks::withTrashed()->where('tenant_id',$current_tenant_id)->where('imported_exam_marks_id',$examMarksDetail['examMarksId'])->first();
            $insertExamMarks[$key]['ExamMarksId'] = $ExamMarksId->id;
            $insertExamMarks[$key]['StudentId'] = $internal_student_data->id;
            $insertExamMarks[$key]['Marks'] = $examMarksDetail['marks'];
            $insertExamMarks[$key]['Remarks'] = $examMarksDetail['remarks'];
            $insertExamMarks[$key]['deleted_at'] =  ($examMarksDetail['isDeleted'] ===  true) ? now() : NULL;
            $insertExamMarks[$key]['imported_exam_detail_id'] = $examMarksDetail['id'];
        }
        $data = collect($insertExamMarks);
        $chunkSize = 500; 
        $data->chunk($chunkSize)->each(function ($chunk) {
            DB::table('exam_marks_details')->insert($chunk->toArray());
        });
    }
    
    public function handleExamMarksGrade($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insertExamGrade = [];
        foreach($finalResponse as $key => $examMarksGrade)
        {

            $existingRecord = ExamGrade::withTrashed()->where('tenant_id',$current_tenant_id)->where('imported_marks_grade_id', $examMarksGrade['id'])->first();
            if ($existingRecord) {
                continue; 
            }

            $currentSessionId = $this->ImportHelper->getSessionId($examMarksGrade);
            $internal_class_id = $this->ImportHelper->getClassId($examMarksGrade);
            if($internal_class_id == null){
                continue;
            }
            $insertExamGrade[$key]['tenant_id'] = $current_tenant_id; 
            $insertExamGrade[$key]['IsActive'] = $examMarksGrade['isActive']; 
            $insertExamGrade[$key]['CreatedBy'] = $examMarksGrade['createdBy']; 
            $insertExamGrade[$key]['SessionId'] = $currentSessionId; 
            $insertExamGrade[$key]['ClassId'] = $internal_class_id; 
            $insertExamGrade[$key]['GradeName'] = $examMarksGrade['gradeName']; 
            $insertExamGrade[$key]['PercentFrom'] = $examMarksGrade['percentFrom']; 
            $insertExamGrade[$key]['PercentUpt'] = $examMarksGrade['percentUpt']; 
            $insertExamGrade[$key]['Description'] = $examMarksGrade['description']; 
            $insertExamGrade[$key]['deleted_at'] = ($examMarksGrade['isDeleted'] ===  true) ? now() : NULL; 
            $insertExamGrade[$key]['imported_marks_grade_id'] = $examMarksGrade['id']; 
             
        }
        $data = collect($insertExamGrade);
        $chunkSize = 500; 
        $data->chunk($chunkSize)->each(function ($chunk) {
            DB::table('marks_grade')->insert($chunk->toArray());
        });
       
    }
}
