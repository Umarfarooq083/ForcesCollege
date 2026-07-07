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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id',100)->nullable();
            $table->integer('SchoolId')->nullable();
             $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('StaffCode',100)->nullable();
            $table->integer('RolesId')->nullable();
            $table->integer('DesignationId')->nullable();
            $table->integer('DepartmentId')->nullable();
            $table->string('FirstName',100)->nullable();
            $table->string('LastName',100)->nullable();
            $table->string('FatherName',100)->nullable();
            $table->string('MotherName',100)->nullable();
            $table->string('Email',100)->nullable();
            $table->string('Gender',20)->nullable();
            $table->timestamp('DateOfBirth')->nullable();
            $table->timestamp('DateOfJoining')->nullable();
            $table->string('Phone',25)->nullable();
            $table->string('EmergencyContactNumber',25)->nullable();
            $table->string('MaritalStatus',20)->nullable();
            $table->string('PhotoPath',100)->nullable();
            $table->string('CurrentAddress',191)->nullable();
            $table->string('PermanentAddress',191)->nullable();
            $table->string('Qualification',100)->nullable();
            $table->string('WorkExperience',100)->nullable();
            $table->string('Note',191)->nullable();
            $table->string('PANNumber',100)->nullable();
            $table->string('EPFNo',100)->nullable();
            $table->string('BasicSalary',100)->nullable();
            $table->string('ContractType',100)->nullable();
            $table->string('WorkShift',100)->nullable();
            $table->string('Location',100)->nullable();
            $table->integer('MedicalLeave')->nullable();
            $table->integer('CasualLeave')->nullable();
            $table->integer('MaternityLeave')->nullable();
            $table->string('AccountTitle',100)->nullable();
            $table->string('BankAccountNumber',100)->nullable();
            $table->string('BankName',100)->nullable();
            $table->integer('CreateUser')->nullable();
            $table->string('IFSCCode',100)->nullable();
            $table->string('BankBranchName',100)->nullable();
            $table->string('FacebookURL',100)->nullable();
            $table->string('TwitterURL',100)->nullable();
            $table->string('LinkedinURL',100)->nullable();
            $table->string('InstagramURL',100)->nullable();
            $table->string('ResumeFilePath',100)->nullable();
            $table->string('JoiningLetterPath',100)->nullable();
            $table->string('OtherDocumentsPath',100)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
