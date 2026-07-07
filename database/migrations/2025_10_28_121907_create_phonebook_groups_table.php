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
        Schema::create('phonebook_groups', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 191)->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('session_id')->nullable();
            $table->text('name')->nullable();
            $table->boolean('is_active')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonebook_groups');
    }
};
