<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_weekly_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->enum('weekend_day', ['saturday', 'sunday', 'both', 'none'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->timestamp('CreatedDate')->nullable();
            $table->timestamp('ModifiedDate')->nullable();
            $table->uuid('tenant_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_weekly_holidays');
    }
};
