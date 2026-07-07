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
        Schema::create('imported_campus_fee_master', function (Blueprint $table) {
            $table->id();
                $table->string('title')->nullable();
                $table->integer('feesTypeNId')->nullable();
                $table->integer('newFeesTypeNId')->nullable();
                $table->integer('imported_fee_master_id')->nullable();
                $table->integer('internal_fee_master_id')->nullable();
                $table->string('amount')->nullable(); 
                $table->integer('classId')->nullable();
                $table->integer('class')->nullable();  
                $table->integer('sectionId')->nullable();  
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
        Schema::dropIfExists('imported_campus_fee_master');
    }
};
