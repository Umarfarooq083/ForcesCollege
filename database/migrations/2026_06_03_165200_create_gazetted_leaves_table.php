<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gazetted_leaves', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 100)->nullable();
            $table->string('title');
            $table->date('date');
            $table->boolean('status')->default(true);
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gazetted_leaves');
    }
};