<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('miscellaneous_payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 100)->nullable();
            $table->unsignedBigInteger('staff_id');
            $table->integer('apply_year');
            $table->integer('applicable_month');
            $table->decimal('amount', 10, 2);
            $table->text('reason')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('miscellaneous_payments');
    }
};