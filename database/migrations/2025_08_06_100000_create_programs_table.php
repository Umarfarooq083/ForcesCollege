<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 100);
            $table->string('name');
            $table->enum('type', ['annual', 'semester']);
            $table->string('duration');
            $table->boolean('IsActive')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};