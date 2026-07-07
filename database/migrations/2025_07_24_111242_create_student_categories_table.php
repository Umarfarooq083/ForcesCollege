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
        Schema::create('student_categories', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id',100);
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->timestamp('CreatedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->timestamp('ModifiedDate')->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('CategoryName')->nullable();
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
        Schema::dropIfExists('student_categories');
    }
};
