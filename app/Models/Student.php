<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'CreatedDate',
        'ModifiedBy',
        'ModifiedDate',
        'SessionId',
        'RollNumber',
        'IsOnlineAdmission',
        'IsStudentEnroll',
        'ClassId',
        'SectionId',
        'AdmissionEnquiryId',
        'FirstName',
        'LastName',
        'Gender',
        'DateOfBirth',
        'BformNo',
        'StudentCategoryId',
        'Religion',
        'promoted_date',
        'Caste',
        'MobileNumber',
        'Email',
        'AdmissionDate',
        'StudentPhotoPath',
        'BloodGroup',
        'StudentHouseId',
        'Height',
        'Weight',
        'AsOnDate',
        'MedicalHistory',
        'FatherName',
        'FatherPhone',
        'FatherOccupation',
        'FatherCnic',
        'FatherPhotoPath',
        'MotherName',
        'MotherPhone',
        'MotherOccupation',
        'MotherPhotoPath',
        'IfGuardianIsValue',
        'GuardianName',
        'GuardianRelation',
        'GuardianEmail',
        'GuardianPhotoPath',
        'GuardianPhone',
        'GuardianOccupation',
        'GuardianAddress',
        'IfGuardianAddressIsCurrentAddress',
        'CurrentAddress',
        'IfPermanentAddressIsCurrentAddress',
        'PermanentAddress',
        'RouteId',
        'HostelId',
        'HostelRoomId',
        'BankAccountNumber',
        'BankName',
        'IFSCCode',
        'NationalIdentificationNumber',
        'LocalIdentificationNumber',
        'RTE',
        'PreviousSchoolDetails',
        'Note',
        'StudentUploadDocumentsTitle1',
        'StudentUploadDocumentPath1',
        'StudentUploadDocumentsTitle2',
        'StudentUploadDocumentPath2',
        'StudentUploadDocumentsTitle3',
        'StudentUploadDocumentPath3',
        'StudentUploadDocumentsTitle4',
        'StudentUploadDocumentPath4',
        'IsDisable',
        'DisableReasonId',
        'Password',
        'MobDeviceId',
        'FcmDeviceToken',
        'Is_Guardian',
        'withdraw_status',
        'withdraw_reason',
        'last_challan_no',
        'last_challan_amount',
        'last_challan_status',
        'withdraw_date',
        'readmitted_date',
        'readmission_reason',
        'readmission_status',
    ];

    public function guardian()
    {
        return $this->hasOne(GuardianInfo::class, 'id', 'guardian_id');
    }

    public function guardianRel()
    {
        return $this->hasOne(GuardianInfo::class, 'id', 'FatherCnic');
    }

    public function class()
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId');
    }
    public function classRel()
    {
        return $this->belongsTo(Classes::class, 'ClassId');
    }

    public function section()
    {
        return $this->hasOne(Section::class, 'id', 'SectionId')->select('id', 'SectionName', 'ClassId', 'SectionType');
    }

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function scopeClass($query,$classId)
    {
        return $query->where('ClassId', $classId);
    }
    
    public function scopeSection($query,$sectionId)
    {
        return $query->where('SectionId', $sectionId);
    }

    public function ExamMarksDetails(){
        return $this->hasMany(ExamMarksDetail::class, 'StudentId', 'id');
    }

    public function studentSession()
    {
        return $this->hasOne(LmsSession::class, 'id', 'SessionId');
    }

    public function studentDocuments()
    {
        return $this->hasMany(StudentDocuments::class, 'student_id', 'id');
    }

    public function disabledReason()
    {
        return $this->hasOne(DisableReason::class, 'id', 'DisableReasonId');
    }

    public function studentAttendance()
    {
        return $this->hasMany(StudentAttendance::class, 'StudentId', 'id');
    }

    public function studentFeeDiscount()
    {
        return $this->hasMany(StudentFeeDiscount::class, 'StudentId', 'id')
            ->select('id','tenant_id','SessionId','StudentId','CampusFeesMasterId',
                    'DiscountAmount','BalanceFeeAfterDiscount','TotalFee');
    }

    public function userFCMToken()
    {
        return $this->hasOne(FcmToken::class, 'user_id', 'id');
    }

}
