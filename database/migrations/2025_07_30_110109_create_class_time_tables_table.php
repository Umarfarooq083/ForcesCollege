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
        Schema::create('class_time_tables', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id',100)->nullable();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->integer('ClassId')->nullable();
            $table->integer('SectionId')->nullable();
            $table->integer('SubjectGroupId')->nullable();
            $table->string('Day', 20)->nullable();
            $table->integer('SubjectId')->nullable();
            $table->integer('StaffId')->nullable();
            $table->time('TimeFrom')->nullable();
            $table->time('TimeTo')->nullable();
            $table->string('RoomNo', 50)->nullable();
            $table->integer('ClassTimeTableGroupId')->nullable();
            $table->softDeletes();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_time_tables');
    }
};
