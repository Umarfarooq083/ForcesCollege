<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    // 2025_09_17_111719_create_imported_sections_table
    public function up(): void
    {
        Schema::create('imported_sections', function (Blueprint $table) {
            $table->id();
            $table->string('sectionName')->nullable();
            $table->integer('classId')->nullable();
            $table->string('isActive')->nullable();
            $table->integer('imported_section_id')->nullable();
            $table->integer('internal_section_id')->nullable();
            $table->string('createdBy')->nullable();
            $table->integer('sessionId')->nullable();
            $table->timestamp('createdDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_sections');
    }
};
