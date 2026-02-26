<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_campaigns', function (Blueprint $table) {
            $table->string('midterm_file')->nullable()->after('evaluation_status');
            $table->text('supervisor_comment')->nullable()->after('rating');
            $table->text('employee_comment')->nullable()->after('supervisor_comment');
        });
    }

    public function down(): void
    {
        Schema::table('user_campaigns', function (Blueprint $table) {
            $table->dropColumn(['midterm_file', 'supervisor_comment', 'employee_comment']);
        });
    }
};
