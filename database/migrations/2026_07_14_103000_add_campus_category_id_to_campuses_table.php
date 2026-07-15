<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->unsignedBigInteger('campus_category_id')->nullable()->after('DomainName');
            $table->foreign('campus_category_id')->references('id')->on('campus_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropForeign(['campus_category_id']);
            $table->dropColumn('campus_category_id');
        });
    }
};
