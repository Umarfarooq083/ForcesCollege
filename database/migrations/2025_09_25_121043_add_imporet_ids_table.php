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
        Schema::table('campus_fees_masters', function (Blueprint $table) {
            $table->integer('import_fee_type_id')->nullable();
            $table->integer('import_fee_master_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_fees_masters', function (Blueprint $table) {
            //
        });
    }
};
