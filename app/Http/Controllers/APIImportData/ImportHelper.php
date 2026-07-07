<?php

namespace App\Http\Controllers\APIImportData;
use App\Models\Api_imported_models\ImportedClasses;
use App\Models\Api_imported_models\ImportedSections;
use App\Models\Api_imported_models\ImportedSessions;
use App\Models\Api_imported_models\ImportedSubjects;
use App\Models\CampusFeesMaster;
use App\Models\Department;
use App\Models\Designation;
use App\Models\ExamSubject;
use App\Models\ExamTerm;
use App\Models\ExamType;
use App\Models\FeesType;
use App\Models\GenerateFeeChallan;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
class ImportHelper
{
    // protected $campusName = 'headoffice';
    // protected $campusName = 'flagshipbwc';
    // protected $campusName = 'premium';   
    // protected $campusName = 'pattoki';
    // protected $campusName = 'aligarden';   
    // protected $campusName = 'makbar';   
    // protected $campusName = 'cantt';   
    // protected $campusName = 'alzahra';   
    // protected $campusName = 'saidusharif';   
    // protected $campusName = 'bahria7';   
    // protected $campusName = 'sahiwal';   
    // protected $campusName = 'bhilomar';   
    // protected $campusName = 'iftikhar';  
    // protected $campusName = 'muzaffarabad';   
    // protected $campusName = 'katlang';   
    // protected $campusName = 'fatima';   
    // protected $campusName = 'rustam';   
    // protected $campusName = 'gujarkhan';   
    protected $campusName = 'alipurchattha';   

    public function getCampusName()
    {
        return $this->campusName;
    }

    public function sendRequest($url)
    {
        ini_set('max_execution_time', 0);
       return Http::withHeaders([
            'Authorization' => 'Fs925WN#123',
            'Cookie' => '.AspNetCore.Session=CfDJ8P75s5epWk1AgB8PmJnWpkVO5%2BZyG0P9cenmYQLmUlOMEZJrpR9Md0l31AQnmiUlCMmBugr9977%2B1Ty%2BQTwf1pYRjQ%2Fw6nPbgiNCPtFKzIygPVeXDRVeR1e3wNM%2B3JAV8aWuob%2FnKm9AjS2mRIuVyfQlolTTd3t7U9lnpPCqQyG2',
        ])
        ->timeout(0)          
        ->connectTimeout(0)    
        ->get($url, [
            'campus' => $this->campusName,
        ]); 
    }

    public function getTenantId()
    {
        ini_set('max_execution_time', 0);
        $tenantData = Tenant::where('domain',$this->campusName)->first();
        return $tenantData->id;
    }

    public function getClassId($object)
    {
        ini_set('max_execution_time', 0);
        $ImportedClassesData = ImportedClasses::where('imported_class_id',$object['classId'])->first();
        if($ImportedClassesData){
            return $ImportedClassesData->internal_class_id;
        }else{
            return null;
        }
    }
    
    public function getSessionId($section)
    {
        ini_set('max_execution_time', 0);
        $ImportedSessionsData = ImportedSessions::where('imported_session_id',$section['sessionId'])->first();
        return $ImportedSessionsData->lms_session_id;
    }

    public function getSectionId($studentCreation)
    {
        ini_set('max_execution_time', 0);
        $ImportedSessionsData = ImportedSections::where('imported_section_id',$studentCreation->sectionId)->first();
        return $ImportedSessionsData->internal_section_id;
    }
    
    public function getStudentId($importedStudent)
    {
        ini_set('max_execution_time', 0);
        return $ImportedSetudentsData = Student::withTrashed()->where('tenant_id',$this->getTenantId())->where('imported_student_id',$importedStudent['studentId'])->first();
        // return $ImportedSetudentsData->id;
    }
   
    public function getFeeTypeId($importedFeeType)
    {
        ini_set('max_execution_time', 0);
        $feeTypeData = FeesType::withTrashed()->where('import_fee_type_id',$importedFeeType['feesTypeNId'])->first();
        return $feeTypeData->id;
    }
   
    public function getCampusFeeMasterId($importedCampusMaster)
    {
        ini_set('max_execution_time', 0);
        $CampusFeeMasterData = CampusFeesMaster::withTrashed()->where('tenant_id',$this->getTenantId())->where('import_fee_master_id',$importedCampusMaster['campusFeesMasterId'])->first();
        return $CampusFeeMasterData->id;
    }
   
    public function getGenerateFeeChallanId($object)
    {
        ini_set('max_execution_time', 0);
        $CampusChallanTranscationData = GenerateFeeChallan::withTrashed()->where('tenant_id',$this->getTenantId())->where('imported_fee_challan_id',$object['generateClassChallanId'])->first();
        return $CampusChallanTranscationData->id;
    }
 
    public function getGenerateFeeChallan($object)
    {
        ini_set('max_execution_time', 0);
        $CampusChallanTranscationData = GenerateFeeChallan::withTrashed()->where('tenant_id',$this->getTenantId())->where('imported_fee_challan_id',$object['generateClassChallanId'])->first();
        return $CampusChallanTranscationData;
    }
   
    public function getExamTermId($object)
    {
        ini_set('max_execution_time', 0);
        $examTermData = ExamTerm::withTrashed()->where('imported_exam_term_id',$object['examTermId'])->first();
        if($examTermData){
            return $examTermData->id;
        }else{
            return 1;
        }     
    }
    
    public function getExamTypeId($object)
    {
        ini_set('max_execution_time', 0);
        $examTpyeData = ExamType::withTrashed()->where('tenant_id',$this->getTenantId())->where('imported_exam_id',$object['examId'])->first();
        return $examTpyeData->id;     
    }
   
    public function getExamSubjectId($object)
    {
        ini_set('max_execution_time', 0);
        $examSubjectData = ImportedSubjects::where('imported_subject_id',$object['subjectId'])->first();
        if($examSubjectData){
            return $examSubjectData->internal_subject_id;     
        }else{
            return null;
        }
    }
    
    public function getExamSubjectDataId($object)
    {
        ini_set('max_execution_time', 0);
        if($object['examSubjectId']){
            $examSubjectData = ExamSubject::withTrashed()->where('tenant_id',$this->getTenantId())->where('imported_exam_subject_id',$object['examSubjectId'])->first();
        return $examSubjectData->id;
        }
             
    }
   
    public function getDepartmentId($object)
    {
        ini_set('max_execution_time', 0);
        $examDepartmentData = Department::withTrashed()->where('imported_department_id',$object['departmentId'])->first();
        if($examDepartmentData){
            return $examDepartmentData->id;
        }else{
            return $examDepartmentData;     
        }
    }
   
    public function getDesiginationId($object)
    {
        ini_set('max_execution_time', 0);
        $examDesignationData = Designation::withTrashed()->where('imported_designation_id',$object['designationId'])->first();
        if($examDesignationData){
            return $examDesignationData->id;
        }else{
            return $examDesignationData;     
        }
    }
    
    public function getStaffId($object)
    {
        ini_set('max_execution_time', 0);
        $StaffData = Staff::withTrashed()->where('imported_staff_id',$object['staffId'])->first();
        return $StaffData->id;
    }


}

