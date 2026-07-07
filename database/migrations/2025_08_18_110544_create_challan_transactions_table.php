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
        Schema::create('challan_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->integer('generate_challan_id')->nullable();
            $table->integer('SchoolId')->nullable();
            $table->string('IsActive')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->integer('FKey')->nullable();
            $table->string('Title')->nullable();
            $table->integer('FeeAmount')->nullable();
            $table->string('TransactionType')->nullable();
            $table->integer('DiscountAmount')->nullable();
            $table->integer('BalanceFeeAfterDiscount')->nullable();
            $table->integer('TotalFee')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challan_transactions');
    }
};
