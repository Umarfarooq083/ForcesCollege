<?php

namespace App\Http\Controllers\APIImportData;

use App\Http\Controllers\Controller;
use App\Jobs\StudentAttendancejob;
use App\Models\Api_imported_models\ImportedCampus;
use App\Models\Api_imported_models\ImportedClasses;
use App\Models\Api_imported_models\ImportedSections;
use App\Models\Api_imported_models\ImportedSessions;
use App\Models\Api_imported_models\ImportedStudentAttendance;
use App\Models\Api_imported_models\ImportedSubjects;
use App\Models\Campus;
use App\Models\CampusClassType;
use App\Models\Classes;
use App\Models\LmsSession;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ImportController extends Controller
{
    protected $baseUrl = 'http://headoffice.fscscampus.com/api/Migration';
    
    protected $ImportHelper;
    protected $ImportStudent;
    protected $ImportFeeStracture;
    protected $ImportHrDepartment;
    public function __construct(ImportHelper $ImportHelper , ImportStudent $ImportStudent, ImportFeeStracture $ImportFeeStracture , ImportHrDepartment $ImportHrDepartment )
    {
        $this->ImportHelper = $ImportHelper;
        $this->ImportStudent = $ImportStudent;
        $this->ImportFeeStracture = $ImportFeeStracture;
        $this->ImportHrDepartment = $ImportHrDepartment;
    }

    public function getApiList()
    {
        return Inertia::render('ApiImport/List');
    }
    
    public function getSessionsList()
    {
        $response = $this->ImportHelper->sendRequest("http://headoffice.fscscampus.com/api/Migration/GetSession");
        $finalResponse = $response->json();
        /* this code will remain commented untill all session are not activated
         * by API Once sessions activated then we will uncomment this code and
         * save data into lsm session table so we can map correctly
         * */ 
        // LmsSession::truncate();
        // foreach($finalResponse as $res)
        // { 
        //     $LmsSession = new LmsSession();
        //     $LmsSession->name = $res['sessionName'];
        //     $LmsSession->start_date = $res['sessionStartDate'];
        //     $LmsSession->end_date = $res['sessionEndDate'];
        //     $LmsSession->status = 1;
        //     $LmsSession->zoneid = 1;
        //     $LmsSession->save();
        // }
        ImportedSessions::truncate();
        foreach($finalResponse as $res){
            $LmsSession = LmsSession::where('start_date',$res['sessionStartDate'])->where('end_date',$res['sessionEndDate'])->first();
            // dump($LmsSession);
            if($LmsSession){
                $createNewImportedSession = new ImportedSessions();
                $createNewImportedSession->sessionName =  $LmsSession->name;
                $createNewImportedSession->sessionStartDate =  $LmsSession->start_date;
                $createNewImportedSession->sessionEndDate =  $LmsSession->end_date;
                $createNewImportedSession->imported_session_id =  $res['id'];
                $createNewImportedSession->lms_session_id =  $LmsSession->id;
                $createNewImportedSession->save();
            }else{
                $LmsSessionData = LmsSession::create([
                    'name' =>$res['sessionName'],
                    'start_date' =>$res['sessionStartDate'],
                    'end_date' =>$res['sessionEndDate'],
                    'status' =>$res['isActive'],
                    'zoneid' =>1,
                    // 'deleted_at' =>($res['isDeleted'] ===  true) ? now() : NULL,
                ]);

                $createNewImportedSessionNew = new ImportedSessions();
                $createNewImportedSessionNew->sessionName =  $LmsSessionData->name;
                $createNewImportedSessionNew->sessionStartDate =  $LmsSessionData->start_date;
                $createNewImportedSessionNew->sessionEndDate =  $LmsSessionData->end_date;
                $createNewImportedSessionNew->imported_session_id =  $res['id'];
                $createNewImportedSessionNew->lms_session_id =  $LmsSessionData->id;
                $createNewImportedSessionNew->save();

            }
        }
        if ($response->successful()) {
            return response()->json(['message' => 'API executed successfully!'], 200);
        }
    }

    public function getCampusList()
    {
        die();
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetAllCampuses');
        $finalResponse = $response->json();
        // ImportedCampus::truncate();
        // ImportedCampus::insert($finalResponse);
        $ImportedCampus = ImportedCampus::get();
        // Campus::truncate();
        // Tenant::truncate();
        // CampusClassType::truncate();
        // DB::table('domains')->truncate();
        foreach($ImportedCampus as $campusCreate)
        {
            if($campusCreate->IsActive){
                $importedCampusDomain = strtolower($campusCreate->DomainName);
                $importedCampusCreate = new Campus();
                $importedCampusCreate->OwnerName = $campusCreate->OwnerName;
                $importedCampusCreate->SchoolName = $campusCreate->SchoolName;
                $importedCampusCreate->Address = $campusCreate->Address;
                $importedCampusCreate->PhoneNo = $campusCreate->PhoneNo;
                $importedCampusCreate->OfficePhone = $campusCreate->OfficePhone;
                $importedCampusCreate->MobileNo = $campusCreate->MobileNo;
                $importedCampusCreate->Area = $campusCreate->Area;
                $importedCampusCreate->URL = $campusCreate->URL;
                $importedCampusCreate->Code = $campusCreate->Code;
                $importedCampusCreate->Rooms = $campusCreate->Rooms;
                $importedCampusCreate->City = $campusCreate->City;
                $importedCampusCreate->EmailAddress = $campusCreate->EmailAddress;
                $importedCampusCreate->TotalFaculty = $campusCreate->TotalFaculty;
                $importedCampusCreate->Rental = ($campusCreate->Rental == true) ? 1 : 0 ;
                $importedCampusCreate->ContractDuration = $campusCreate->ContractDuration;
                $importedCampusCreate->Comments = $campusCreate->Comments;
                $importedCampusCreate->Other = $campusCreate->Other;
                $importedCampusCreate->AgreementPath = $campusCreate->AgreementPath;
                $importedCampusCreate->SchoolType = $campusCreate->SchoolType;
                $importedCampusCreate->AccountNo = $campusCreate->AccountNo;
                $importedCampusCreate->DomainName = $importedCampusDomain;
                $importedCampusCreate->BranchCode = $campusCreate->BranchCode;
                $importedCampusCreate->Logo = $campusCreate->Logo;
                $importedCampusCreate->IsAvailableForMobApp = 1;
                $importedCampusCreate->zoneid = 1;
                $importedCampusCreate->SortOrder = 0;
                $importedCampusCreate->SchoolId = $campusCreate->SchoolId;
                $importedCampusCreate->IsActive = $campusCreate->IsActive;
                $importedCampusCreate->CreatedBy = $campusCreate->CreatedBy;
                $importedCampusCreate->CreatedDate = $campusCreate->CreatedDate;
                $importedCampusCreate->ModifiedBy = $campusCreate->ModifiedBy;
                $importedCampusCreate->ModifiedDate = $campusCreate->ModifiedDate;
                $importedCampusCreate->SessionId = $campusCreate->SessionId;
                $importedCampusCreate->save();
                
                $tenant = Tenant::create([
                    'domain' => $importedCampusDomain,
                    'name' => $campusCreate->SchoolName,
                ]);

                $importedCampusCreate->update([
                    'tenant_id' => $tenant->id,
                ]);

                $CampusClassType = new CampusClassType();
                $CampusClassType->campus_id = $importedCampusCreate->id;
                $CampusClassType->class_type_id = 1;
                $CampusClassType->save();

                $baseDomain = env('BASE_TENANT_DOMAIN');
                $fullDomain = "{$importedCampusDomain}.{$baseDomain}";
                
                $tenant->domains()->create([
                'domain' => $fullDomain,
                ]);
            } 
        }

        if ($response->successful()) {
            return response()->json(['message' => 'API executed successfully!'], 200);
        }
    }

    public function getClassesList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetClass');
        $finalResponse = $response->json();
        ImportedClasses::truncate();
        foreach($finalResponse as $importedClass)
        {
            $ImportedClasses = new ImportedClasses();
            $ImportedClasses->className = $importedClass['className'];
            $ImportedClasses->imported_class_id = $importedClass['id'];
            $ImportedClasses->isActive = $importedClass['isActive'];
            $ImportedClasses->save();
        }
        $ImportedClassesData = ImportedClasses::get();
        foreach($ImportedClassesData as $classes)
        {
            $ClassesExistdata = Classes::where('ClassName',$classes['className'])->first();
            if($ClassesExistdata){
                ImportedClasses::where('id',$classes['id'])->update([
                    'internal_class_id' => $ClassesExistdata->id
                ]);
            }else{
                $createClasses = new Classes();
                $createClasses->ClassName = $classes['className'];
                $createClasses->IsActive = 1;
                $createClasses->CreatedBy = auth()->user()->id;
                $createClasses->class_type_id = 1;
                $createClasses->save();

                ImportedClasses::where('id',$classes['id'])->update([
                    'internal_class_id' => $createClasses->id
                ]);

            }
            // $classes
        }
       

        // Classes::truncate();
        // foreach($finalResponse as $class)
        // {
        //     $createClasses = new Classes();
        //     $createClasses->tenant_id = tenant('id');
        //     $createClasses->ClassName = $class['className'];
        //     $createClasses->IsActive = ($class['isActive'] == true) ? 1 : 0;
        //     $createClasses->CreatedBy = auth()->user()->id;
        //     $createClasses->class_type_id = 1;
        //     $createClasses->save();
        // }
        


    }
   
    public function getSectionList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetSection');
        $finalResponse = $response->json();
        ImportedSections::truncate();
        foreach($finalResponse as $importedSectionCreate)
        {
            $createImportSection = new ImportedSections();
            $createImportSection->sectionName = $importedSectionCreate['sectionName'];
            $createImportSection->classId = $importedSectionCreate['classId'];
            $createImportSection->imported_section_id = $importedSectionCreate['id'];
            $createImportSection->isActive = $importedSectionCreate['isActive'];
            $createImportSection->createdBy = $importedSectionCreate['createdBy'];
            $createImportSection->sessionId = $importedSectionCreate['sessionId'];
            $createImportSection->save();
        }
        $alreadyimportedSections = ImportedSections::get();
        $current_tenant_id = $this->ImportHelper->getTenantId();
        foreach($alreadyimportedSections as $section)
        { 
            $internal_class_id = $this->ImportHelper->getClassId($section);
            $currentSessionId = $this->ImportHelper->getSessionId($section);
            $existonSectionData = Section::where('tenant_id',$current_tenant_id)
                ->where('SectionName',$section['sectionName'])
                ->where('ClassId',$internal_class_id)
                ->first();
            if($existonSectionData){
                ImportedSections::where('id',$section->id)->update([
                    'internal_section_id' => $existonSectionData->id
                ]);
            }else{
                $createSection = new Section();
                $createSection->tenant_id = $current_tenant_id;
                $createSection->SessionId = $currentSessionId;
                $createSection->SectionName = $section['sectionName'];
                $createSection->ClassId = $internal_class_id;
                $createSection->IsActive = 1;
                $createSection->CreatedBy = auth()->user()->id;
                $createSection->SectionType = 1;
                $createSection->Strength = 25;
                $createSection->save();

                ImportedSections::where('id',$section->id)->update([
                    'internal_section_id' => $createSection->id
                ]);
                
            }
           
        }
    }
    
    public function getSubjectList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetSubject');
        $finalResponse = $response->json();
        $current_tenant_id = $this->ImportHelper->getTenantId();
        ImportedSubjects::truncate();
        foreach($finalResponse as $importedSubject)
        {
            $ImportedSubjectsCreate = new ImportedSubjects();
            $ImportedSubjectsCreate->subjectName = $importedSubject['subjectName'];
            $ImportedSubjectsCreate->subjectType = $importedSubject['subjectType'];
            $ImportedSubjectsCreate->subjectCode = $importedSubject['subjectCode'];
            $ImportedSubjectsCreate->classId = $importedSubject['classId'];
            $ImportedSubjectsCreate->isActive = $importedSubject['isActive'];
            $ImportedSubjectsCreate->imported_subject_id = $importedSubject['id'];
            $ImportedSubjectsCreate->save();
        }

        $ImportedSubjectsData = ImportedSubjects::get();
        foreach($ImportedSubjectsData as $importedSubject)
        {
            $internal_class_id = $this->ImportHelper->getClassId($importedSubject);
            $existonSubjectData = Subject::where('tenant_id',$current_tenant_id)
                ->where('SubjectName',$importedSubject['subjectName'])
                ->where('ClassId',$internal_class_id)
                ->first();

            if($existonSubjectData){
                ImportedSubjects::where('id',$importedSubject['id'])->update([
                    'internal_subject_id' => $existonSubjectData->id 
                ]);
            }else{
                $createSubject = new Subject();
                $createSubject->tenant_id = $current_tenant_id;
                $createSubject->SubjectName = $importedSubject['subjectName'];
                $createSubject->SubjectType = 1;
                $createSubject->SubjectCode = $importedSubject['subjectCode'];
                $createSubject->ClassId = $internal_class_id;
                $createSubject->IsActive = 1;
                $createSubject->CreatedBy = auth()->user()->id;
                $createSubject->save();
                ImportedSubjects::where('id',$importedSubject['id'])->update([
                    'internal_subject_id' => $createSubject->id 
                ]);
            }
            
        }
    }

    public function getStudentList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetStudents');
        $finalResponse = $response->json();
        $this->ImportStudent->handleStudent($finalResponse); 
    }
   
    public function getStudentInquiryLostList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetAdmissionEnquiryLost');
        $finalResponse = $response->json();
        $this->ImportStudent->handleStudentInquiry($finalResponse); 
    }
    
    public function getStudentAttendanceList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetStudentAttendance');
        $finalResponse = $response->json();
        $chunks = collect($finalResponse)->chunk(1000);
        ImportedStudentAttendance::truncate();
        foreach ($chunks as $chunk) {
            StudentAttendanceJob::dispatch($chunk->values()->all(),$this->ImportHelper);
        }
    }

    public function getFeeStracture()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetFeeStructure');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleFeeStracture($finalResponse);
    }
    
    public function getOptionalFeeMapping()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetStudentOptionalFees');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleFeeMapping($finalResponse);
    }
    
    public function getStudentFeeDiscount()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetStudentDiscounts');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleStudentFeeDiscount($finalResponse);
    }
   
    public function getStudentFeeChallan()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetStudentChallans');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleStudentFeeChallan($finalResponse);
    }
   
    public function getStudentFeeChallanTranscation()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetChallanTransactions');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleStudentFeeChallanTranscation($finalResponse);
    }
  
    public function getStudentChallanPartialPayment()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetChallanPartialPayments');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleStudentChallanPartialPayment($finalResponse);
    }
    
    public function getStudentChallanArrears()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetChallanArrears');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleStudentChallanArrears($finalResponse);
    }
   
    public function getExamTerms()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetExamTerm');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleExamTerms($finalResponse);
    }
   
    public function getImportExamTypes()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetExam');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleExamTypes($finalResponse);
    }
    
    public function getImportExamSubject()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetExamSubjects');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleExamSubject($finalResponse);
    }
   
    public function getImportExamStudent()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetExamStudents');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleExamStudent($finalResponse);
    }
    
    public function getImportExamMarks()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetExamMarks');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleExamMarks($finalResponse);
    }
   
    public function getImportExamMarksDetail()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetExamMarksDetail');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleExamMarksDetail($finalResponse);
    }
    
    public function getImportExamMarksGrade()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetMarksGrades');
        $finalResponse = $response->json();
        $this->ImportFeeStracture->handleExamMarksGrade($finalResponse);
    }
   
    public function getDepartmentList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetDepartment');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleDepartmentList($finalResponse);
    }
   
    public function getDesignationList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetDesignation');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleDesignationList($finalResponse);
    }
  
    public function getStaffList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetStaff');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleStaffList($finalResponse);
    }
   
    public function getAssignClassTeacherList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetAssignClassTeacher');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleAssignClassTeacherList($finalResponse);
    }
   
    public function getClassTimeTable()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetClassTimeTable');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleClassTimeTable($finalResponse);
    }
   
    public function getUserList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetUser');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleGetUserList($finalResponse);
    }
    
    public function getRolesList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetRole');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleGetRolesList($finalResponse);
    }
   
    public function getUploadContentGroupList()
    {
        ini_set('max_execution_time', 0);
        $campusName = $this->ImportHelper->getCampusName();
        if($campusName === 'headoffice'){
            $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetUploadContentGroup');
            $finalResponse = $response->json();
            $this->ImportHrDepartment->handleGetUploadContentGroupList($finalResponse);
        }
    }
   
    public function getUploadContentList()
    {
        ini_set('max_execution_time', 0);
        $campusName = $this->ImportHelper->getCampusName();
        if($campusName === 'headoffice'){
            $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetUploadContent');
            $finalResponse = $response->json();
            $this->ImportHrDepartment->handleGetUploadContentList($finalResponse);
        }
    }
    
    public function getUploadContentLogList()
    {
        ini_set('memory_limit', '5120M');
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetDownloadContentLog');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleGetUploadContentLogsList($finalResponse);
    }
    
    public function getHomeWorkList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetHomework');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handlegetHomeWorkList($finalResponse);
    }
   
    public function getSMSCreditList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetSMSCredit');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handlegetSMSCreditList($finalResponse);
    }
 
    public function getSMSLogList()
    {
        ini_set('max_execution_time', 0);
        $response = $this->ImportHelper->sendRequest($this->baseUrl.'/GetSMSLog');
        $finalResponse = $response->json();
        $this->ImportHrDepartment->handleGetSMSLogList($finalResponse);
    }
}

