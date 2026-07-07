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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id',100);
            $table->string('SubjectName')->nullable();
            $table->string('SubjectType')->nullable();
            $table->string('SubjectCode')->nullable();
            $table->string('ClassId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('SessionId')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->integer('SchoolId')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('StudentCategoryGroupId')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
