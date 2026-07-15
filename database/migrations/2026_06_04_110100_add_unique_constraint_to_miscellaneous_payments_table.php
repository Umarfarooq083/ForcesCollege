<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('miscellaneous_payments', function (Blueprint $table) {
            $table->unique(['tenant_id', 'staff_id', 'apply_year', 'applicable_month'], 'miscellaneous_payments_unique_staff_year_month');
        });
    }

    public function down(): void
    {
        Schema::table('miscellaneous_payments', function (Blueprint $table) {
            $table->dropUnique('miscellaneous_payments_unique_staff_year_month');
        });
    }
};
