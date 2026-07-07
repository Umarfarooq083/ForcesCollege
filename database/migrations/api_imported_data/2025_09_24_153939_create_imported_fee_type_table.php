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
        Schema::create('imported_fee_type', function (Blueprint $table) {
            $table->id();
            $table->string('feesCode')->nullable();
            $table->integer('imported_fee_type_id')->nullable();
            $table->integer('internal_fee_type_id')->nullable();
            $table->string('feeName')->nullable(); 
            $table->string('description')->nullable();
            $table->string('feeCycle')->nullable();  
            $table->string('applicableMonth')->nullable();  
            $table->string('isOptional')->nullable(); 
            $table->string('isRefundable')->nullable(); 
            $table->string('campusFeesMastersExist')->nullable();
            $table->string('isActive')->nullable();
            $table->string('isDeleted')->nullable();
            $table->integer('createdBy')->nullable();
            $table->timestamp('createdDate')->nullable();
            $table->integer('modifiedBy')->nullable();
            $table->timestamp('modifiedDate')->nullable();
            $table->integer('sessionId')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_fee_type');
    }
};
