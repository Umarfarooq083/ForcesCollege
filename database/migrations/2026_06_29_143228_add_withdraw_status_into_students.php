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
        Schema::table('students', function (Blueprint $table) {
            $table->string('withdraw_status')->nullable();
            $table->date('withdraw_date')->nullable();
            $table->string('withdraw_reason')->nullable();
            $table->string('last_challan_no')->nullable();
            $table->string('last_challan_amount')->nullable();
            $table->string('last_challan_status')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['withdraw_status', 'withdraw_date', 'withdraw_reason', 'last_challan_no', 'last_challan_amount', 'last_challan_status']);
        });
    }
};
