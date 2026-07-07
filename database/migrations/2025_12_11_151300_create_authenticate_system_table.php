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
        Schema::create('authenticate_system', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('tenant_domain_name')->nullable();
            $table->string('system_generated_key');
            $table->string('user_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authenticate_system');
    }
};
