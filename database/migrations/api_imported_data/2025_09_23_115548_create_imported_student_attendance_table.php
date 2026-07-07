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
        Schema::create('imported_student_attendance', function (Blueprint $table) {
            $table->id();
            $table->integer('studentId')->nullable(); 
            $table->string('attendanceType')->nullable(); 
            $table->timestamp('attendanceDate')->nullable(); 
            $table->integer('imported_attendance_id')->nullable(); 
            $table->integer('internal_attendance_id')->nullable();  
            $table->integer('sessionId')->nullable(); 
            $table->string('note')->nullable(); 
            $table->string('isFromMachine')->nullable(); 
            $table->string('isActive')->nullable(); 
            $table->integer('createdBy')->nullable(); 
            $table->timestamp('createdDate')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_student_attendance');
    }
};
