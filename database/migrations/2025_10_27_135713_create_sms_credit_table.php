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
        Schema::create('sms_credit', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->integer('smsCreditCount')->nullable();
            $table->integer('schoolId')->nullable();
            $table->boolean('isActive')->nullable();
            $table->integer('createdBy')->nullable();
            $table->integer('modifiedBy')->nullable();
            $table->integer('sessionId')->nullable();
            $table->integer('imported_sms_credit_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_credit');
    }
};
