<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_slips', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedTinyInteger('payroll_month');
            $table->unsignedSmallInteger('payroll_year');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('computer_allowance', 10, 2)->default(0);
            $table->decimal('mobile_allowance', 10, 2)->default(0);
            $table->decimal('recreation_allowance', 10, 2)->default(0);
            $table->unsignedInteger('gazetted_leaves_count')->default(0);
            $table->unsignedInteger('working_days');
            $table->unsignedInteger('present_days')->default(0);
            $table->unsignedInteger('absent_days')->default(0);
            $table->unsignedInteger('leave_days')->default(0);
            $table->decimal('total_absent_deduction', 10, 2)->default(0);
            $table->decimal('gazetted_leave_deduction', 10, 2)->default(0);
            $table->decimal('fine_deduction', 10, 2)->default(0);
            $table->decimal('miscellaneous_payment', 10, 2)->default(0);
            $table->decimal('salary_tax', 10, 2)->default(0);
            $table->decimal('security_refund', 10, 2)->default(0);
            $table->decimal('security_deduction', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->string('status', 50)->default('generated');
            $table->unsignedBigInteger('CreatedBy')->nullable();
            $table->unsignedBigInteger('ModifiedBy')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_slips');
    }
};