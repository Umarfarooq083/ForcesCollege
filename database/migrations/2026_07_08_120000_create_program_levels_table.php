<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_levels', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 100);
            $table->unsignedBigInteger('programm_id');
            $table->string('title');
            $table->boolean('status')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('programm_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_levels');
    }
};