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
        Schema::create('challan_partial_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->string('tenant_id')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->integer('GenerateClassChallanId')->nullable();
            $table->decimal('ReceivedAmount', 10, 2)->default(0);
            $table->date('CollectDate')->nullable();
            $table->integer('CollectBy')->nullable();
            $table->string('PaymentMode')->nullable();
            $table->date('SubmitDate')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challan_partial_payments');
    }
};
