<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add midterm date columns
        Schema::table('campaigns', function (Blueprint $table) {
            $table->date('midterm_starts_at')->nullable()->after('objective_stops_at');
            $table->date('midterm_stops_at')->nullable()->after('midterm_starts_at');
        });

        // Alter status enum to include midterm phases
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN status ENUM('draft','objective_in_progress','objective_completed','midterm_in_progress','midterm_completed','evaluation_in_progress','evaluation_completed','archived') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN status ENUM('draft','objective_in_progress','objective_completed','evaluation_in_progress','evaluation_completed','archived') DEFAULT 'draft'");

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['midterm_starts_at', 'midterm_stops_at']);
        });
    }
};
