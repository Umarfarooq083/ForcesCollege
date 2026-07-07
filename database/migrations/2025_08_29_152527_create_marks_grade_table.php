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
        Schema::create('marks_grade', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->integer('ClassId')->nullable();
            $table->string('GradeName')->nullable();
            $table->integer('PercentFrom')->nullable();
            $table->integer('PercentUpt')->nullable();
            $table->string('Description')->nullable();
            $table->integer('MarksGradeGroupId')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks_grade');
    }
};
