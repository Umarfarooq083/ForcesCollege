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
        Schema::create('content_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_name',100)->nullable();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->string('job_position')->nullable();
            $table->string('summary')->nullable();
            $table->string('content_title')->nullable();
            $table->string('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_feedback');
    }
};
