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
        Schema::create('sectiones', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id',100)->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('SectionName',100);
            $table->integer('ClassId');
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive',100)->default(true);
            $table->string('CreatedBy',100);
            $table->integer('ModifiedBy')->nullable();
            $table->string('ClassGroupId',100)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sectiones');
    }
};
