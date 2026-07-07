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
        Schema::create('exam_students', function (Blueprint $table) {
            $table->id();
            $table->integer('SchoolId')->nullable();
            $table->string('tenant_id')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ClassId')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->integer('ExamId')->nullable();
            $table->integer('ExamSubjectId')->nullable();
            $table->integer('StudentId')->nullable();
            $table->integer('ExamStudentGroupId')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_students');
    }
};
