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
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('Title')->nullable();
            $table->integer('ExamId')->nullable();
            $table->integer('SubjectId')->nullable();
            $table->date('Date')->nullable();
            $table->time('Time')->nullable();
            $table->integer('Duration')->nullable();
            $table->integer('CreditHours')->nullable();
            $table->integer('RoomNo')->nullable();
            $table->integer('MarksMax')->nullable();
            $table->integer('MarksMin')->nullable();
            $table->integer('ExamSubjectGroupId')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_subjects');
    }
};
