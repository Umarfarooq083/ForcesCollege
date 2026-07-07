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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id',100);
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->timestamp('CreatedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->timestamp('ModifiedDate')->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('RollNumber')->nullable();
            $table->boolean('IsOnlineAdmission')->default(false);
            $table->boolean('IsStudentEnroll')->default(false);
            $table->integer('ClassId')->nullable();
            $table->integer('SectionId')->nullable();
            $table->integer('AdmissionEnquiryId')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('Gender')->nullable();
            $table->date('DateOfBirth')->nullable();
            $table->string('BformNo')->nullable();
            $table->integer('StudentCategoryId')->nullable();
            $table->string('Religion')->nullable();
            $table->string('Caste')->nullable();
            $table->string('MobileNumber')->nullable();
            $table->string('Email')->nullable();
            $table->date('AdmissionDate')->nullable();
            $table->string('StudentPhotoPath')->nullable();
            $table->string('BloodGroup')->nullable();
            $table->integer('StudentHouseId')->nullable();
            $table->string('Height')->nullable();
            $table->string('Weight')->nullable();
            $table->date('AsOnDate')->nullable();
            $table->text('MedicalHistory')->nullable();
            $table->string('FatherName')->nullable();
            $table->string('FatherPhone')->nullable();
            $table->string('FatherOccupation')->nullable();
            $table->string('FatherCnic')->nullable();
            $table->string('FatherPhotoPath')->nullable();
            $table->string('MotherName')->nullable();
            $table->string('MotherPhone')->nullable();
            $table->string('MotherOccupation')->nullable();
            $table->string('MotherPhotoPath')->nullable();
            $table->boolean('IfGuardianIsValue')->default(false);
            $table->string('GuardianName')->nullable();
            $table->string('GuardianRelation')->nullable();
            $table->string('GuardianEmail')->nullable();
            $table->string('GuardianPhotoPath')->nullable();
            $table->string('GuardianPhone')->nullable();
            $table->string('GuardianOccupation')->nullable();
            $table->text('GuardianAddress')->nullable();
            $table->boolean('IfGuardianAddressIsCurrentAddress')->default(false);
            $table->text('CurrentAddress')->nullable();
            $table->boolean('IfPermanentAddressIsCurrentAddress')->default(false);
            $table->text('PermanentAddress')->nullable();
            $table->integer('RouteId')->nullable();
            $table->integer('HostelId')->nullable();
            $table->integer('HostelRoomId')->nullable();
            $table->string('BankAccountNumber')->nullable();
            $table->string('BankName')->nullable();
            $table->string('IFSCCode')->nullable();
            $table->string('NationalIdentificationNumber')->nullable();
            $table->string('LocalIdentificationNumber')->nullable();
            $table->boolean('RTE')->default(false);
            $table->text('PreviousSchoolDetails')->nullable();
            $table->text('Note')->nullable();
            $table->string('StudentUploadDocumentsTitle1')->nullable();
            $table->string('StudentUploadDocumentPath1')->nullable();
            $table->string('StudentUploadDocumentsTitle2')->nullable();
            $table->string('StudentUploadDocumentPath2')->nullable();
            $table->string('StudentUploadDocumentsTitle3')->nullable();
            $table->string('StudentUploadDocumentPath3')->nullable();
            $table->string('StudentUploadDocumentsTitle4')->nullable();
            $table->string('StudentUploadDocumentPath4')->nullable();
            $table->boolean('IsDisable')->default(false);
            $table->integer('DisableReasonId')->nullable();
            $table->string('Password')->nullable();
            $table->string('MobDeviceId')->nullable();
            $table->text('FcmDeviceToken')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
