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
        Schema::create('imported_lost_inquiry', function (Blueprint $table) {
            $table->id();
            $table->integer('internal_student_inquiry')->nullable();                             
            $table->string('name')->nullable();                             
            $table->string('studentName')->nullable();                              
            $table->string('phone')->nullable();                                
            $table->string('email')->nullable();                                
            $table->string('address')->nullable();                              
            $table->string('status')->nullable();                               
            $table->string('sendNotification')->nullable();                             
            $table->string('description')->nullable();                              
            $table->string('note')->nullable();                             
            $table->timestamp('date')->nullable();                           
            $table->string('nextFollowUpDate')->nullable();                            
            $table->integer('assignedId')->nullable();                               
            $table->string('assigned')->nullable();                             
            $table->integer('referenceId')->nullable();                              
            $table->integer('reference')->nullable();                                
            $table->integer('sourceId')->nullable();                             
            $table->string('source')->nullable();                               
            $table->string('numberOfChild')->nullable();                                
            $table->integer('classId')->nullable();                              
            $table->string('class')->nullable();                                
            $table->integer('admissionEnquiryGroupId')->nullable();                              
            $table->string('admissionEnquiryGroup')->nullable();                                
            $table->string('isSmsSent')->nullable();                                
            $table->string('students')->nullable();                                                      
            $table->integer('schoolId')->nullable();                             
            $table->string('isActive')->nullable();                             
            $table->string('isDeleted')->nullable();                                
            $table->integer('createdBy')->nullable();                                
            $table->timestamp('createdDate')->nullable();                             
            $table->integer('modifiedBy')->nullable();                               
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
        Schema::dropIfExists('imported_lost_inquiry');
    }
};
