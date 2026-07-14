<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 100)->nullable();
            $table->unsignedBigInteger('staff_id');
            $table->decimal('amount', 10, 2);
            $table->text('reason')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->unique(['staff_id'], 'salary_taxes_staff_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_taxes');
    }
};
