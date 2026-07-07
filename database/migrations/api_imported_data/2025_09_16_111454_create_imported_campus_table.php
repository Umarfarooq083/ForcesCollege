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
        Schema::create('imported_campus', function (Blueprint $table) {
            $table->id();
            $table->string('OwnerName')->nullable();
            $table->string('SchoolName')->nullable();
            $table->string('Address')->nullable();
            $table->string('PhoneNo')->nullable();
            $table->string('OfficePhone')->nullable();
            $table->integer('SchoolId')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->boolean('IsDeleted')->default(false);
            $table->integer('CreatedBy')->nullable();
            $table->timestamp('CreatedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->timestamp('ModifiedDate')->nullable();
            $table->integer('SessionId')->nullable();
            $table->string('session')->nullable();
            $table->string('MobileNo')->nullable();
            $table->string('Area')->nullable();
            $table->integer('Rooms')->nullable();
            $table->string('City')->nullable();
            $table->string('EmailAddress')->nullable();
            $table->integer('TotalFaculty')->nullable();
            $table->string('Rental')->nullable();
            $table->integer('ContractDuration')->nullable(); // in months or years?
            $table->text('Comments')->nullable();
            $table->text('Other')->nullable();
            $table->string('AgreementPath')->nullable();
            $table->string('SchoolType')->nullable();
            $table->string('URL')->nullable();
            $table->string('Code')->nullable();
            $table->string('AccountNo')->nullable();
            $table->string('BranchCode')->nullable();
            $table->string('DomainName')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->string('Logo')->nullable();
            $table->boolean('IsAvailableForMobApp')->default(false);
            $table->integer('SortOrder')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_campus');
    }
};
