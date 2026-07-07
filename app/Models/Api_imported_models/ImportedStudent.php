<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedStudent extends Model
{
    protected $table = 'imported_student';  
    protected $fillable = [
        'rollNumber','imported_student_id','internal_student_id','internal_enquiry_id','isOnlineAdmission','isStudentEnroll',
        'classId','class','sectionId','section','admissionEnquiryId','admissionEnquiry','firstName','lastName','gender',
        'dateOfBirth','bformNo','studentCategoryId','studentCategory','religion','caste','password','mobileNumber','email',
        'admissionDate','studentPhotoPath','bloodGroup','studentHouseId','studentHouse','height','weight','asOnDate','medicalHistory',
        'fatherName','fatherPhone','fatherOccupation','fatherCnic','fatherPhotoPath','motherName','motherPhone','motherOccupation',
        'motherPhotoPath','ifGuardianIsValue','guardianName','guardianRelation','guardianEmail','guardianPhotoPath','guardianPhone',
        'guardianOccupation','guardianAddress','ifGuardianAddressIsCurrentAddress','currentAddress','ifPermanentAddressIsCurrentAddress',
        'permanentAddress','routeId','route','hostelId','hostel','hostelRoomId','hostelRoom','bankAccountNumber','bankName','ifscCode',
        'nationalIdentificationNumber','localIdentificationNumber','rte','previousSchoolDetails','note','mobDeviceId','fcmDeviceToken',
        'studentUploadDocumentsTitle1','studentUploadDocumentPath1','studentUploadDocumentsTitle2','studentUploadDocumentPath2',
        'studentUploadDocumentsTitle3','studentUploadDocumentPath3','studentUploadDocumentsTitle4','studentUploadDocumentPath4',
        'feeMappings','collectFees','studentAttendance','attendanceMachineMapping','discountMappings','isDisable','disableReasonId',
        'disableReason','attendanceType','attendanceDate','isAbsentExam','marksMax','marksMin','obtainMarks','examId','examSubjectId',
        'examRemarks','machineId','attMachineLogDateTime','isFromMachine','optionalFeeMappings','studentFeeDiscounts','generateClassChallans',
        'schoolId','isActive','isDeleted','createdBy','createdDate','modifiedBy','modifiedDate','sessionId','session'
    ];
}
