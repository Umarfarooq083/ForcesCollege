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
        Schema::table('generate_fee_challan', function (Blueprint $table) {
            $table->integer('per_day_fine')->nullable()->after('challan_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generate_fee_challan', function (Blueprint $table) {
            $table->dropColumn('per_day_fine');
        });
    }
};
