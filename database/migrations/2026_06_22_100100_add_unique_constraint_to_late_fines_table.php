<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('late_fines', function (Blueprint $table) {
            $table->unique(['tenant_id', 'staff_id', 'apply_year', 'applicable_month'], 'late_fines_unique_staff_year_month');
        });
    }

    public function down(): void
    {
        Schema::table('late_fines', function (Blueprint $table) {
            $table->dropUnique('late_fines_unique_staff_year_month');
        });
    }
};