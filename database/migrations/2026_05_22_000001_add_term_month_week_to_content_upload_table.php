<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_upload', function (Blueprint $table) {
            $table->string('termId', 50)->nullable()->after('subjectId');
            $table->string('monthId', 50)->nullable()->after('termId');
            $table->string('weekId', 50)->nullable()->after('monthId');
        });
    }

    public function down(): void
    {
        Schema::table('content_upload', function (Blueprint $table) {
            $table->dropColumn(['termId', 'monthId', 'weekId']);
        });
    }
};
