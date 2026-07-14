<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->date('readmitted_date')->nullable();
            $table->string('readmission_reason', 255)->nullable();
            $table->boolean('readmission_status')->default(false);
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['readmitted_date', 'readmission_reason', 'readmission_status']);
        });
    }
};
