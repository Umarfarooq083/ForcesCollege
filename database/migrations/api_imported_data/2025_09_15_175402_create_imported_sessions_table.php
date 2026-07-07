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
        Schema::create('imported_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('sessionName');
            $table->timestamp('sessionStartDate')->nullable();
            $table->timestamp('sessionEndDate')->nullable();
            $table->integer('imported_session_id')->nullable();
            $table->integer('lms_session_id')->nullable()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_sessions');
    }
};
