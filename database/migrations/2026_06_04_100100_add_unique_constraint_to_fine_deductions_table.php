<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fine_deductions', function (Blueprint $table) {
            $table->unique(['tenant_id', 'staff_id', 'apply_year', 'applicable_month'], 'fine_deductions_unique_staff_year_month');
        });
    }

    public function down(): void
    {
        Schema::table('fine_deductions', function (Blueprint $table) {
            $table->dropUnique('fine_deductions_unique_staff_year_month');
        });
    }
};