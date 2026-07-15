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
        Schema::create('optional_fee_mapping', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 100)->nullable();
            $table->integer('FeesTypeNId')->nullable();
            $table->integer('ClassId')->nullable();
            $table->integer('SectionId')->nullable();
            $table->integer('StudentId')->nullable();
            $table->timestamp('FromMonth')->nullable();
            $table->timestamp('ToMonth')->nullable();
            $table->integer('CampusFeesMasterId')->nullable();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('SessionId')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optional_fee_mapping');
    }
};
