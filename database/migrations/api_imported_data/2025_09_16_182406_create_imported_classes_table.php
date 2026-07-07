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
        Schema::create('imported_classes', function (Blueprint $table) {
            $table->id();
            $table->string('className',100);
            $table->integer('imported_class_id')->nullable();
            $table->integer('internal_class_id')->nullable();
            $table->boolean('isActive',100)->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_classes');
    }
};
