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
        Schema::create('student_inquiries', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->integer('SessionId')->nullable();
            $table->string('Name')->nullable();
             $table->string('LastName',100)->nullable();
            $table->string('StudentName')->nullable();
            $table->string('Phone')->nullable();
            $table->string('Email')->nullable();
            $table->string('Address')->nullable();
            $table->timestamp('Date')->nullable();
            $table->integer('ClassId')->nullable();
            $table->timestamp('BirthDate')->useCurrent();
            $table->string('Gender');
            $table->string('PreviousInstitute',191)->nullable();
            $table->string('FatherName',100)->nullable();
            $table->string('FatherPhoneNo',500)->nullable();
            $table->string('MotherName',100)->nullable();
            $table->string('MotherPhoneNo',50)->nullable();
            $table->string('Description')->nullable();
            $table->integer('Status')->default(0)->nullable();
            $table->string('SchoolId')->nullable();
            $table->string('IsActive')->nullable();
            $table->string('Note')->nullable();
            $table->timestamp('NextFollowUpDate')->nullable();
            $table->integer('AssignedId')->nullable();
            $table->integer('ReferenceId')->nullable();
            $table->integer('SourceId')->nullable();
            $table->integer('NumberOfChild')->nullable();
            $table->integer('AdmissionEnquiryGroupId')->nullable();
            $table->integer('IsSmsSent')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_inquiry');
    }
};
