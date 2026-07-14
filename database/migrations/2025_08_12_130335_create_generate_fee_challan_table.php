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
        Schema::create('generate_fee_challan', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->integer('challan_no')->nullable();
            $table->integer('SchoolId')->nullable();
            $table->string('IsActive')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->integer('StudentId')->nullable();
            $table->date('ChallanMonth')->nullable();
            $table->date('DueDate')->nullable();
            $table->date('ExpiryDate')->nullable();
            $table->string('Status', 50)->nullable();
            $table->date('SubmitDate')->nullable();
            $table->string('PaymentMode')->nullable();
            $table->integer('FineAmount')->nullable();
            $table->integer('WaivedFineAmount')->nullable();
            $table->string('Note')->nullable();
            $table->date('CollectDate')->nullable();
            $table->integer('CollectBy')->nullable();
            $table->string('IsPartialPayment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generate_fee_challan');
    }
};
