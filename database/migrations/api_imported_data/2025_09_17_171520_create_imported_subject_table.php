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
        Schema::create('imported_subject', function (Blueprint $table) {
            $table->id();
            $table->string('subjectName')->nullable();
            $table->string('subjectType')->nullable();
            $table->string('subjectCode')->nullable();
            $table->integer('classId')->nullable();
            $table->string('isActive')->nullable();
            $table->integer('sessionId')->nullable();
            $table->integer('imported_subject_id')->nullable();
            $table->integer('internal_subject_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_subject');
    }
};
