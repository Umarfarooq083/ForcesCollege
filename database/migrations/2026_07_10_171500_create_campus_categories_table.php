<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->boolean('IsDeleted')->default(false);
            $table->integer('CreatedBy')->nullable();
            $table->timestamp('CreatedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->timestamp('ModifiedDate')->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('CategoryName')->nullable();
            $table->string('CategoryCode')->nullable();
            $table->string('CategoryType')->nullable();
            $table->string('Description')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->integer('SortOrder')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_categories');
    }
};
