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
        Schema::create('content_upload', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 100)->nullable();
            $table->string('ContentTitle', 100)->nullable();
            $table->string('ContentType', 100)->nullable();
            $table->integer('ClassId')->nullable();
            $table->integer('SectionId')->nullable();
            $table->integer('subjectId')->nullable();
            $table->timestamp('UploadDate')->nullable();
            $table->string('Description')->nullable();
            $table->string('ContentFilePath')->nullable();
            $table->string('AvailableForAllCampuses')->nullable();
            $table->string('AvailableForAllClasses')->nullable();
            $table->integer('UploadContentGroupId')->nullable();
            $table->string('AllowedSchools')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SchoolId')->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('DisableReasonName')->nullable();
            $table->string('SuperAdmin')->nullable();
            $table->string('Student')->nullable();
            $table->integer('DisableReasonGroupId')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_upload');
    }
};
