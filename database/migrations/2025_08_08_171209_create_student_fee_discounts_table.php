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
        Schema::create('student_fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('SchoolId')->nullable();
            $table->string('IsActive')->nullable();
            $table->string('CreatedBy')->nullable();
            $table->string('ModifiedBy')->nullable();
            $table->string('SessionId')->nullable();
            $table->string('StudentId')->nullable();
            $table->string('CampusFeesMasterId')->nullable();
            $table->string('DiscountAmount')->nullable();
            $table->string('BalanceFeeAfterDiscount')->nullable();
            $table->string('DiscountType')->nullable();
            $table->string('TotalFee')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fee_discounts');
    }
};
