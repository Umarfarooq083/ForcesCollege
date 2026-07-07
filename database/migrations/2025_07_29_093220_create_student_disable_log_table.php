<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('student_disable_log', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->string('tenant_id')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->date('FromDate')->nullable();
            $table->date('ToDate')->nullable();
            $table->string('Reason')->nullable();
            $table->enum('Type', ['Enable', 'Disable']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_disable_log');
    }
};
