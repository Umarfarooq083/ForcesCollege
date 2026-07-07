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
        Schema::table('sectiones', function (Blueprint $table) {
            $table->integer('SectionType')->nullable()->after('ClassGroupId');
            $table->integer('Strength')->default(25)->after('SectionType');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sectiones', function (Blueprint $table) {
            $table->dropColumn(['SectionType', 'Strength']);
        });
    }
};
