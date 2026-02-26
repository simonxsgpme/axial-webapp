<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->string('acronym')->nullable()->after('name');
            $table->uuid('parent_uuid')->nullable()->after('category');
            $table->foreign('parent_uuid')->references('uuid')->on('entities')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->dropForeign(['parent_uuid']);
            $table->dropColumn(['parent_uuid', 'acronym']);
        });
    }
};
