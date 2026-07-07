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
        Schema::table('optional_fee_mapping', function (Blueprint $table) {
            $table->date('FromMonth')->change();
            $table->date('ToMonth')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('optional_fee_mapping', function (Blueprint $table) {
            $table->timestamp('FromMonth')->change();
            $table->timestamp('ToMonth')->change();
        });
    }
};
