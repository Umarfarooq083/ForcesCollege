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
        Schema::create('home_works', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id',191);
            $table->integer('classId')->nullable();
            $table->string('class')->nullable();
            $table->integer('sectionId')->nullable();
            $table->string('section')->nullable();
            $table->integer('subjectGroupId')->nullable();
            $table->string('subjectGroup')->nullable();
            $table->integer('subjectId')->nullable();
            $table->string('subject')->nullable();
            $table->date('homeworkDate')->nullable();
            $table->date('submissionDate')->nullable();
            $table->string('attachDocumentPath')->nullable();
            $table->text('description')->nullable();
            $table->integer('homeworkGroupId')->nullable();
            $table->string('homeworkGroup')->nullable();
            $table->string('session')->nullable();
            $table->integer('schoolId')->default(0);
            $table->boolean('isActive')->default(true);
            $table->integer('createdBy')->nullable();
            $table->integer('modifiedBy')->nullable();
            $table->integer('sessionId')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_works');
    }
};
