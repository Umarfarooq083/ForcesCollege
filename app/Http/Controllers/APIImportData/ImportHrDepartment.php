<?php

namespace App\Http\Controllers\APIImportData;

use App\Models\Api_imported_models\ImportedSections;
use App\Models\Api_imported_models\ImportedSubjects;
use App\Models\AssignClassTeacher;
use App\Models\ClassTimeTable;
use App\Models\ContentUpload;
use App\Models\Department;
use App\Models\Designation;
use App\Models\DownloadContentLog;
use App\Models\HomeWork;
use App\Models\Roles;
use App\Models\SmsCredits;
use App\Models\SMSLog;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\UploadCampusContent;
use App\Models\UploadContentGroup;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ImportHrDepartment
{
    protected $ImportHelper;
    public function __construct(ImportHelper $ImportHelper)
    {
        $this->ImportHelper = $ImportHelper;
    }

    public function handleDepartmentList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $insert_department = [];
        foreach ($finalResponse as $key => $department) {
            $existingRecord = Department::withTrashed()
                ->where('DepartmentName', $department['departmentName'])
                ->where('Code', $department['code'])
                // ->where('imported_department_id', $department['id'])
                ->first();
            if ($existingRecord) {
                continue;
            }
            $insert_department[$key]['DepartmentName'] = $department['departmentName'];
            $insert_department[$key]['Code'] = $department['code'];
            $insert_department[$key]['IsActive'] = $department['isActive'];
            $insert_department[$key]['CreatedBy'] = $department['createdBy'];
            $insert_department[$key]['deleted_at'] = ($department['isDeleted'] ===  true) ? now() : NULL;
            $insert_department[$key]['imported_department_id'] = $department['id'];
        }
        Department::insert($insert_department);
    }

    public function handleDesignationList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $insert_designation = [];
        foreach ($finalResponse as $key => $designation) {
            $existingRecord = Designation::withTrashed()
                ->where('DesignationName', $designation['designationName'])
                ->first();
            if ($existingRecord) {
                $existingRecord->update([
                    'imported_designation_id' => $designation['id']
                ]);
                continue;
            }
            $insert_designation[$key]['IsActive'] = $designation['isActive'];
            $insert_designation[$key]['CreatedBy'] = $designation['createdBy'];
            $insert_designation[$key]['DesignationName'] = $designation['designationName'];
            $insert_designation[$key]['deleted_at'] = ($designation['isDeleted'] ===  true) ? now() : NULL;
            $insert_designation[$key]['imported_designation_id'] = $designation['id'];
        }
        Designation::insert($insert_designation);
    }

    public function handleStaffList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insertStaff = [];
        foreach ($finalResponse as $key => $staff) {
            $existingRecord = Staff::withTrashed()->where('tenant_id', $current_tenant_id)->where('imported_staff_id', $staff['id'])->first();
            if ($existingRecord) {
                continue;
            }
            $current_department_id = $this->ImportHelper->getDepartmentId($staff);
            $current_desigination_id = $this->ImportHelper->getDesiginationId($staff);
            $insertStaff[$key]['tenant_id'] = $current_tenant_id;
            $insertStaff[$key]['IsActive'] = $staff['isActive'];
            $insertStaff[$key]['SessionId'] = 1;
            $insertStaff[$key]['StaffCode'] = $staff['staffCode'];
            $insertStaff[$key]['RolesId'] = 1;
            $insertStaff[$key]['DesignationId'] = $current_desigination_id;
            $insertStaff[$key]['DepartmentId'] = $current_department_id;
            $insertStaff[$key]['FirstName'] = $staff['firstName'];
            $insertStaff[$key]['LastName'] = $staff['lastName'];
            $insertStaff[$key]['FatherName'] = $staff['fatherName'];
            $insertStaff[$key]['MotherName'] = $staff['motherName'];
            $insertStaff[$key]['Email'] = $staff['email'];
            $insertStaff[$key]['Gender'] = $staff['gender'];
            $insertStaff[$key]['DateOfBirth'] = $staff['dateOfBirth'];
            $insertStaff[$key]['DateOfJoining'] = $staff['dateOfJoining'];
            $insertStaff[$key]['Phone'] = $staff['phone'];
            $insertStaff[$key]['EmergencyContactNumber'] = $staff['emergencyContactNumber'];
            $insertStaff[$key]['MaritalStatus'] = $staff['maritalStatus'];
            $insertStaff[$key]['CurrentAddress'] = $staff['currentAddress'];
            $insertStaff[$key]['PermanentAddress'] = $staff['permanentAddress'];
            $insertStaff[$key]['Qualification'] = $staff['qualification'];
            $insertStaff[$key]['WorkExperience'] = $staff['workExperience'];
            $insertStaff[$key]['Note'] = $staff['note'];
            $insertStaff[$key]['BasicSalary'] = $staff['basicSalary'];
            $insertStaff[$key]['CreateUser'] = $staff['createUser'];
            $insertStaff[$key]['ContractType'] = $staff['contractType'];
            $insertStaff[$key]['deleted_at'] = ($staff['isDeleted'] ===  true) ? now() : NULL;
            $insertStaff[$key]['imported_staff_id'] = $staff['id'];
        }
        Staff::insert($insertStaff);
    }

    public function handleAssignClassTeacherList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insertAssignClassTeacher = [];
        foreach ($finalResponse as $key => $AssignTeacher) {
            $existingRecord = AssignClassTeacher::withTrashed()->where('tenant_id', $current_tenant_id)->where('imported_assign_class_teacher_id', $AssignTeacher['id'])->first();
            if ($existingRecord) {
                continue;
            }
            $internal_class_id = $this->ImportHelper->getClassId($AssignTeacher);
            $internal_staff_id = $this->ImportHelper->getStaffId($AssignTeacher);
            $ImportedSessionsData = ImportedSections::where('imported_section_id', $AssignTeacher['sectionId'])->first();
            $insertAssignClassTeacher[$key]['tenant_id'] = $current_tenant_id;
            $insertAssignClassTeacher[$key]['ClassId'] = $internal_class_id;
            $insertAssignClassTeacher[$key]['SectionId'] = $ImportedSessionsData->internal_section_id;
            $insertAssignClassTeacher[$key]['StaffId'] = $internal_staff_id;
            $insertAssignClassTeacher[$key]['IsActive'] = $AssignTeacher['isActive'];
            $insertAssignClassTeacher[$key]['CreatedBy'] = $AssignTeacher['createdBy'];
            $insertAssignClassTeacher[$key]['deleted_at'] = ($AssignTeacher['isDeleted'] ===  true) ? now() : NULL;
            $insertAssignClassTeacher[$key]['imported_assign_class_teacher_id'] = $AssignTeacher['id'];
        }
        AssignClassTeacher::insert($insertAssignClassTeacher);
    }

    public function handleClassTimeTable($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insertClassTimeTable = [];
        foreach ($finalResponse as $key => $classTimeTable) {
            $existingRecord = ClassTimeTable::withTrashed()->where('tenant_id', $current_tenant_id)->where('imported_class_time_table', $classTimeTable['id'])->first();
            if ($existingRecord) {
                continue;
            }

            $insertClassTimeTable[$key]['tenant_id'] = $current_tenant_id;
            $internal_class_id = $this->ImportHelper->getClassId($classTimeTable);
            $internal_subject_id = $this->ImportHelper->getExamSubjectId($classTimeTable);
            $internal_staff_id = $this->ImportHelper->getStaffId($classTimeTable);
            $ImportedSessionsData = ImportedSections::where('imported_section_id', $classTimeTable['sectionId'])->first();
            $timeFrom = date("H:i:s", strtotime($classTimeTable['timeFrom']));
            $timeTo = date("H:i:s", strtotime($classTimeTable['timeTo']));

            $insertClassTimeTable[$key]['IsActive'] = $classTimeTable['isActive'];
            $insertClassTimeTable[$key]['CreatedBy'] = $classTimeTable['createdBy'];
            $insertClassTimeTable[$key]['ClassId'] = $internal_class_id;
            $insertClassTimeTable[$key]['SectionId'] = $ImportedSessionsData->internal_section_id;
            $insertClassTimeTable[$key]['SubjectId'] = $internal_subject_id;
            $insertClassTimeTable[$key]['StaffId'] = $internal_staff_id;
            $insertClassTimeTable[$key]['Day'] = $classTimeTable['day'];
            $insertClassTimeTable[$key]['date'] = $classTimeTable['createdDate'];
            $insertClassTimeTable[$key]['TimeFrom'] = $timeFrom;
            $insertClassTimeTable[$key]['TimeTo'] = $timeTo;
            $insertClassTimeTable[$key]['RoomNo'] = $classTimeTable['roomNo'];
            $insertClassTimeTable[$key]['ClassTimeTableGroupId'] = $classTimeTable['classTimeTableGroupId'];
            $insertClassTimeTable[$key]['deleted_at'] = ($classTimeTable['isDeleted'] ===  true) ? now() : NULL;
            $insertClassTimeTable[$key]['imported_class_time_table'] = $classTimeTable['id'];
        }
        ClassTimeTable::insert($insertClassTimeTable);
    }

    public function handleGetUserList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insertUserList = [];
        foreach ($finalResponse as $key => $userList) {
            $existingRecord = User::withTrashed()->where('tenant_id', $current_tenant_id)->where('imported_user_id', $userList['id'])->first();
            if ($existingRecord) {
                continue;
            }

            $insertUserList[$key]['tenant_id'] = $current_tenant_id;
            $insertUserList[$key]['name'] = $userList['username'];
            $insertUserList[$key]['email'] = $userList['email'];
            $insertUserList[$key]['password'] = Hash::make($userList['password']);
            $insertUserList[$key]['phone_no'] = $userList['mobile'];
            $insertUserList[$key]['createdBy'] = $userList['createdBy'];
            $insertUserList[$key]['deleted_at'] = ($userList['isDeleted'] ===  true) ? now() : NULL;
            $insertUserList[$key]['imported_user_id'] = $userList['id'];
        }
        User::insert($insertUserList);
    }

    public function handleGetRolesList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $insertRoleList = [];
        foreach ($finalResponse as $key => $roleList) {
            $existingRecord = Roles::withTrashed()->where('name', $roleList['roleName'])->first();
            if ($existingRecord) {
                $existingRecord->update([
                    'imported_role_id' => $roleList['id']
                ]);
                continue;
            }
            $insertRoleList[$key]['name'] = $roleList['roleName'];
            $insertRoleList[$key]['status'] = ($roleList['isActive'] ===  true) ? 'active' : NULL;
            $insertRoleList[$key]['deleted_at'] = ($roleList['isDeleted'] ===  true) ? now() : NULL;
            $insertRoleList[$key]['imported_role_id'] = $roleList['id'];
        }
        Roles::insert($insertRoleList);
    }

    public function handleGetUploadContentGroupList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $insertContentGroupList = [];
        foreach ($finalResponse as $key => $contentGroup) {
            $existingRecord = UploadContentGroup::where('imported_content_group_id', $contentGroup['id'])->first();
            if ($existingRecord) {
                continue;
            }
            $insertContentGroupList[$key]['name'] = $contentGroup['name'];
            $insertContentGroupList[$key]['Category'] = $contentGroup['category'];
            $insertContentGroupList[$key]['CategoryId'] = 1;
            $insertContentGroupList[$key]['IsActive'] = $contentGroup['isActive'];
            $insertContentGroupList[$key]['CreatedBy'] = $contentGroup['createdBy'];
            $insertContentGroupList[$key]['deleted_at'] = ($contentGroup['isDeleted'] ===  true) ? now() : NULL;
            $insertContentGroupList[$key]['imported_content_group_id'] = $contentGroup['id'];
        }
        UploadContentGroup::insert($insertContentGroupList);
    }

    public function handleGetUploadContentList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $existingIds = ContentUpload::withTrashed()
            ->pluck('imported_upload_content_id')
            ->flip();

        $contentGroups = UploadContentGroup::pluck(
            'id',
            'imported_content_group_id'
        );

        $sections = ImportedSections::pluck(
            'internal_section_id',
            'imported_section_id'
        );

        $tenants = Tenant::with('CampusRel')
            ->get()
            ->keyBy('domain');

        $insertContentList = [];
        $campusContentInsert = [];

        foreach ($finalResponse as $contentUpload) {


            if (isset($existingIds[$contentUpload['id']])) {
                continue;
            }

            if (!isset($contentGroups[$contentUpload['uploadContentGroupId']])) {
                continue;
            }

            $groupId = $contentGroups[$contentUpload['uploadContentGroupId']];


            if (!empty($contentUpload['allowedSchools'])) {
                $domains = explode(',', $contentUpload['allowedSchools']);
                foreach ($domains as $domain) {
                    if (!isset($tenants[$domain])) {
                        continue;
                    }
                    $campusContentInsert[] = [
                        'tenant_id'         => $tenants[$domain]->id,
                        'campus_id'         => optional($tenants[$domain]->CampusRel)->id,
                        'upload_content_id' => $groupId,
                    ];
                }
            }

            $insertContentList[] = [
                'subjectId'                     => $contentUpload['subjectId']
                    ? $this->ImportHelper->getExamSubjectId($contentUpload)
                    : null,

                'SectionId'                     => $contentUpload['sectionId']
                    ? ($sections[$contentUpload['sectionId']] ?? null)
                    : null,

                'ClassId'                       => $contentUpload['classId']
                    ? $this->ImportHelper->getClassId($contentUpload)
                    : null,

                'UploadContentGroupId'          => $groupId,
                'ContentTitle'                  => $contentUpload['contentTitle'],
                'ContentType'                   => $contentUpload['contentType'],
                'UploadDate'                    => $contentUpload['uploadDate'],
                'Description'                   => $contentUpload['description'],
                'ContentFilePath'               => $contentUpload['contentFilePath'],
                'AvailableForAllCampuses'       => $contentUpload['availableForAllCampuses'],
                'AvailableForAllClasses'        => $contentUpload['availableForAllClasses'],
                'IsActive'                      => $contentUpload['isActive'],
                'CreatedBy'                     => $contentUpload['createdBy'],
                'SuperAdmin'                    => $contentUpload['superAdmin'],
                'deleted_at'                    => $contentUpload['isDeleted'] ? now() : null,
                'imported_upload_content_id'    => $contentUpload['id'],
                'created_at'                    => now(),
                'updated_at'                    => now(),
            ];
        }

        collect($insertContentList)->chunk(500)->each(function ($chunk) {
            DB::table('content_upload')->insert($chunk->toArray());
        });

        collect($campusContentInsert)->chunk(500)->each(function ($chunk) {
            DB::table('upload_campus_content')->insert($chunk->toArray());
        });
    }


    public function handleGetUploadContentLogsList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $tenantdata = Tenant::pluck('id', 'domain')->toArray();
        $contentUploadData = ContentUpload::withTrashed()
            ->pluck('id', 'imported_upload_content_id')
            ->toArray();

        $existingIds = DownloadContentLog::pluck('important_content_upload_log_id')->toArray();
        $existingIds = array_flip($existingIds);

        $chunks = collect($finalResponse)->chunk(1000);

        foreach ($chunks as $chunk) {
            $insertData = [];
            foreach ($chunk as $innerChunk) {
                if (isset($existingIds[$innerChunk['id']])) {
                    continue;
                }

                if (!isset($tenantdata[$innerChunk['campusName']])) {
                    continue;
                }

                $tenantId = $tenantdata[$innerChunk['campusName']];
                $uploadContentId = $contentUploadData[$innerChunk['uploadContentId']] ?? 1;

                $insertData[] = [
                    'tenant_id' => $tenantId,
                    'user_id' => 1,
                    'created_at' => $innerChunk['createdDate'],
                    'updated_at' => $innerChunk['downloadTime'],
                    'domainName' => $innerChunk['campusName'],
                    'upload_content_id' => $uploadContentId,
                    'important_content_upload_log_id' => $innerChunk['id'],
                ];
            }

            if (!empty($insertData)) {
                DB::table('download_content_logs')->insert($insertData);
            }
        }
    }

    public function handleGetHomeWorkList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $existingRecord = HomeWork::withTrashed()->where('tenant_id', $current_tenant_id)->pluck('imported_home_work_id')->flip();

        $sectionsMap = ImportedSections::pluck('internal_section_id', 'imported_section_id');
        $subjectsMap = ImportedSubjects::pluck('internal_subject_id', 'imported_subject_id');
        $insertHomeWork = [];
        foreach ($finalResponse as $homeWork) {
            if (isset($existingRecord[$homeWork['id']])) {
                continue;
            }
            $internal_class_id = $this->ImportHelper->getClassId($homeWork);
            $section_id = $sectionsMap[$homeWork['sectionId']] ?? null;
            $subject_id = $subjectsMap[$homeWork['subjectId']] ?? null;

            if (!$section_id || !$subject_id) {
                continue;
            }

            $insertHomeWork[] = [
                'tenant_id'              => $current_tenant_id,
                'classId'                => $internal_class_id,
                'sectionId'              => $section_id,
                'subjectId'              => $subject_id,
                'homeworkDate'           => $homeWork['homeworkDate'],
                'submissionDate'         => $homeWork['submissionDate'],
                'description'            => $homeWork['description'],
                'isActive'               => $homeWork['isActive'],
                'deleted_at'             => ($homeWork['isDeleted'] === true) ? now() : null,
                'imported_home_work_id'  => $homeWork['id'],
            ];
        }

        collect($insertHomeWork)
            ->chunk(500)
            ->each(fn($chunk) => DB::table('home_works')->insert($chunk->toArray()));
    }


    public function handlegetSMSCreditList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $insertSmsCredit = [];
        foreach ($finalResponse as $key => $smsCredit) {
            $existingRecord = SmsCredits::where('imported_sms_credit_id', $smsCredit['id'])->first();
            if ($existingRecord) {
                continue;
            }
            $currentSessionId = $this->ImportHelper->getSessionId($smsCredit);

            $insertSmsCredit[$key]['tenant_id'] = $current_tenant_id;
            $insertSmsCredit[$key]['smsCreditCount'] = $smsCredit['smsCreditCount'];
            $insertSmsCredit[$key]['isActive'] = $smsCredit['isActive'];
            $insertSmsCredit[$key]['deleted_at'] = ($smsCredit['isDeleted'] ===  true) ? now() : NULL;
            $insertSmsCredit[$key]['createdBy'] = $smsCredit['createdBy'];
            $insertSmsCredit[$key]['sessionId'] = $currentSessionId;
            $insertSmsCredit[$key]['created_at'] = $smsCredit['createdDate'];
            $insertSmsCredit[$key]['imported_sms_credit_id'] = $smsCredit['id'];
        }

        $data = collect($insertSmsCredit);
        $chunkSize = 500;
        $data->chunk($chunkSize)->each(function ($chunk) {
            DB::table('sms_credit')->insert($chunk->toArray());
        });
    }

    public function handleGetSMSLogList($finalResponse)
    {
        ini_set('max_execution_time', 0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $existingRecord = SMSLog::withTrashed()->pluck('imported_sms_log_id')->flip();

        $insertSmsLog = [];
        foreach ($finalResponse as $key => $smsLog) {
            if (isset($existingRecord[$smsLog['id']])) {
                continue;
            }

            $currentSessionId = $this->ImportHelper->getSessionId($smsLog);
            $insertSmsLog[$key]['tenant_id'] = $current_tenant_id;
            $insertSmsLog[$key]['mobileNo'] = $smsLog['mobileNo'];
            $insertSmsLog[$key]['body'] = $smsLog['body'];
            $insertSmsLog[$key]['characterLength'] = $smsLog['characterLength'];
            $insertSmsLog[$key]['smsCount'] = $smsLog['smsCount'];
            $insertSmsLog[$key]['status'] = $smsLog['status'];
            $insertSmsLog[$key]['apiCode'] = $smsLog['apiCode'];
            $insertSmsLog[$key]['apItype'] = $smsLog['apItype'];
            $insertSmsLog[$key]['apiResponseText'] = $smsLog['apiResponseText'];
            $insertSmsLog[$key]['apiTransactionID'] = $smsLog['apiTransactionID'];
            $insertSmsLog[$key]['isActive'] = $smsLog['isActive'];
            $insertSmsLog[$key]['createdBy'] = $smsLog['createdBy'];
            $insertSmsLog[$key]['sessionId'] = $currentSessionId;
            $insertSmsLog[$key]['deleted_at'] = ($smsLog['isDeleted'] ===  true) ? now() : NULL;
            $insertSmsLog[$key]['imported_sms_log_id'] = $smsLog['id'];
            $insertSmsLog[$key]['created_at'] = $smsLog['createdDate'];
        }
        $data = collect($insertSmsLog);
        $chunkSize = 500;
        $data->chunk($chunkSize)->each(function ($chunk) {
            DB::table('sms_log')->insert($chunk->toArray());
        });
    }
}
