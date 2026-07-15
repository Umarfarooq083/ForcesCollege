<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('TransportAllowance', 100)->nullable();
            $table->string('ComputerAllowance', 100)->nullable();
            $table->string('MobileAllowance', 100)->nullable();
            $table->string('RecreationAllowance', 100)->nullable();
            $table->boolean('HasProvidentFund')->default(false)->nullable();
            $table->string('ProvidentFundAmount', 100)->nullable();
            $table->boolean('HasEOBI')->default(false)->nullable();
            $table->string('EOBIAmount', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'TransportAllowance',
                'ComputerAllowance',
                'MobileAllowance',
                'RecreationAllowance',
                'HasProvidentFund',
                'ProvidentFundAmount',
                'HasEOBI',
                'EOBIAmount',
            ]);
        });
    }
};
