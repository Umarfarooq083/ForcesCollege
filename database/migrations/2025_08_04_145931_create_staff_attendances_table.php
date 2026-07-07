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
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id',100)->nullable();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->integer('StaffId')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('Attendance')->nullable();
            $table->text('Note')->nullable();
            $table->boolean('IsHolidayToday')->default(false);
            $table->date('AttendanceDate')->nullable();
            $table->integer('StaffAttendanceGroupId')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
