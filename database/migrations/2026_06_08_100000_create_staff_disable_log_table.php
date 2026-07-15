<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_disable_log', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id');
            $table->string('tenant_id')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->date('FromDate')->nullable();
            $table->date('ToDate')->nullable();
            $table->string('Reason')->nullable();
            $table->enum('Type', ['Enable', 'Disable']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_disable_log');
    }
};
