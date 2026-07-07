<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('imported_student', function (Blueprint $table) {
            $table->id();
            $table->string('rollNumber')->nullable();
            $table->integer('imported_student_id')->nullable();
            $table->integer('internal_student_id')->nullable();
            $table->integer('internal_enquiry_id')->nullable();
            $table->integer('internal_guardian_id')->nullable();
            $table->string('isOnlineAdmission', 50)->nullable();  
            $table->string('isStudentEnroll', 50)->nullable();
            $table->integer('classId')->nullable();
            $table->string('class')->nullable();  
            $table->integer('sectionId')->nullable();  
            $table->string('section')->nullable();
            $table->integer('admissionEnquiryId')->nullable(); 
            $table->string('admissionEnquiry', 50)->nullable();
            $table->string('firstName', 50)->nullable();  
            $table->string('lastName', 50)->nullable();
            $table->string('gender', 50)->nullable(); 
            $table->timestamp('dateOfBirth')->nullable();
            $table->string('bformNo', 50)->nullable();
            $table->string('studentCategoryId', 11)->nullable();  
            $table->string('studentCategory', 11)->nullable();
            $table->string('religion', 11)->nullable();
            $table->string('caste', 11)->nullable();  
            $table->string('password', 50)->nullable();
            $table->string('mobileNumber', 50)->nullable();
            $table->string('email', 50)->nullable();  
            $table->timestamp('admissionDate')->nullable();  
            $table->string('studentPhotoPath', 50)->nullable();
            $table->string('bloodGroup', 11)->nullable(); 
            $table->string('studentHouseId', 11)->nullable(); 
            $table->string('studentHouse', 11)->nullable();
            $table->string('height', 50)->nullable(); 
            $table->string('weight', 50)->nullable(); 
            $table->string('asOnDate',100)->nullable();
            $table->string('medicalHistory', 50)->nullable(); 
            $table->string('fatherName', 50)->nullable(); 
            $table->string('fatherPhone', 50)->nullable();
            $table->string('fatherOccupation', 50)->nullable();
            $table->string('fatherCnic', 50)->nullable(); 
            $table->string('fatherPhotoPath', 50)->nullable();
            $table->string('motherName', 50)->nullable(); 
            $table->string('motherPhone', 50)->nullable();
            $table->string('motherOccupation', 50)->nullable();
            $table->string('motherPhotoPath', 50)->nullable();
            $table->string('ifGuardianIsValue', 50)->nullable();  
            $table->string('guardianName', 50)->nullable();
            $table->string('guardianRelation', 50)->nullable();
            $table->string('guardianEmail', 50)->nullable();  
            $table->string('guardianPhotoPath', 50)->nullable();  
            $table->string('guardianPhone', 50)->nullable();  
            $table->string('guardianOccupation', 50)->nullable(); 
            $table->string('guardianAddress', 50)->nullable();
            $table->string('ifGuardianAddressIsCurrentAddress', 50)->nullable();  
            $table->string('currentAddress', 100)->nullable();  
            $table->string('ifPermanentAddressIsCurrentAddress', 50)->nullable(); 
            $table->string('permanentAddress', 191)->nullable();
            $table->string('routeId', 50)->nullable();
            $table->string('route', 50)->nullable();  
            $table->string('hostelId', 50)->nullable();
            $table->string('hostel', 50)->nullable(); 
            $table->string('hostelRoomId', 50)->nullable();
            $table->string('hostelRoom', 50)->nullable(); 
            $table->string('bankAccountNumber', 50)->nullable();  
            $table->string('bankName', 50)->nullable();
            $table->string('ifscCode', 50)->nullable();
            $table->string('nationalIdentificationNumber', 50)->nullable();
            $table->string('localIdentificationNumber', 50)->nullable();  
            $table->string('rte', 50)->nullable();
            $table->string('previousSchoolDetails', 50)->nullable();  
            $table->string('note', 50)->nullable();
            $table->string('mobDeviceId', 50)->nullable();
            $table->string('fcmDeviceToken', 50)->nullable(); 
            $table->text('studentUploadDocumentsTitle1', 50)->nullable();
            $table->text('studentUploadDocumentPath1', 50)->nullable(); 
            $table->text('studentUploadDocumentsTitle2', 50)->nullable();
            $table->text('studentUploadDocumentPath2', 50)->nullable(); 
            $table->text('studentUploadDocumentsTitle3', 50)->nullable();
            $table->text('studentUploadDocumentPath3', 50)->nullable(); 
            $table->text('studentUploadDocumentsTitle4', 50)->nullable();
            $table->text('studentUploadDocumentPath4', 50)->nullable(); 
            $table->string('feeMappings', 50)->nullable();
            $table->string('collectFees', 50)->nullable();
            $table->string('studentAttendance', 50)->nullable();  
            $table->string('attendanceMachineMapping', 50)->nullable();
            $table->string('discountMappings', 50)->nullable();
            $table->string('isDisable', 50)->nullable();  
            $table->string('disableReasonId', 50)->nullable();
            $table->string('disableReason', 50)->nullable();  
            $table->string('attendanceType', 50)->nullable(); 
            $table->string('attendanceDate', 50)->nullable(); 
            $table->string('isAbsentExam', 50)->nullable();
            $table->string('marksMax', 50)->nullable();
            $table->string('marksMin', 50)->nullable();
            $table->string('obtainMarks', 50)->nullable();
            $table->string('examId', 50)->nullable(); 
            $table->string('examSubjectId', 50)->nullable();  
            $table->string('examRemarks', 50)->nullable();
            $table->string('machineId', 50)->nullable();  
            $table->string('attMachineLogDateTime', 50)->nullable();  
            $table->string('isFromMachine', 50)->nullable();  
            $table->string('optionalFeeMappings', 50)->nullable();
            $table->string('studentFeeDiscounts', 50)->nullable();
            $table->string('generateClassChallans', 50)->nullable();  
            $table->string('schoolId', 50)->nullable();
            $table->string('isActive', 50)->nullable();
            $table->string('isDeleted', 50)->nullable();  
            $table->string('createdBy', 50)->nullable();  
            $table->timestamp('createdDate')->nullable();
            $table->string('modifiedBy',100)->nullable(); 
            $table->timestamp('modifiedDate')->nullable();
            $table->integer('sessionId')->nullable();  
            $table->string('session')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_student');
    }
};
