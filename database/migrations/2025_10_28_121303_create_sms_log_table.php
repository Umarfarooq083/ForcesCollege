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
        Schema::create('sms_log', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('mobileNo')->nullable();
            $table->string('body')->nullable();
            $table->string('characterLength')->nullable();
            $table->integer('smsCount')->nullable();
            $table->integer('status')->nullable();
            $table->string('apiCode')->nullable();
            $table->string('apItype')->nullable();
            $table->string('apiResponseText')->nullable();
            $table->string('apiTransactionID')->nullable();
            $table->boolean('isActive')->nullable();
            $table->integer('createdBy')->nullable();
            $table->integer('modifiedBy')->nullable();
            $table->integer('sessionId')->nullable();
            $table->integer('imported_sms_log_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_log');
    }
};
