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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true)->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->integer('ClassId')->nullable();
            $table->integer('SectionId')->nullable();
            $table->integer('StudentId')->nullable();
            $table->string('AttendanceType', 50)->nullable();
            $table->date('AttendanceDate')->nullable();
            $table->text('Note')->nullable();
            $table->boolean('IsFromMachine')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
