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
        Schema::create('fees_type', function (Blueprint $table) {
            $table->id();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('FeesCode')->nullable();
            $table->string('FeeName')->nullable();
            $table->string('Description')->nullable();
            $table->string('FeeCycle')->nullable();
            $table->string('ApplicableMonth')->nullable();
            $table->boolean('IsOptional')->default(true);
            $table->boolean('IsRefundable')->default(true);
            $table->boolean('Isroyality')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees_type');
    }
};
