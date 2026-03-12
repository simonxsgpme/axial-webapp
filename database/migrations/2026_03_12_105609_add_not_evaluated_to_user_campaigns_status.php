<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_campaigns', function (Blueprint $table) {
            // Modifier objective_status pour ajouter 'not_evaluated'
            DB::statement("ALTER TABLE user_campaigns MODIFY COLUMN objective_status ENUM('draft', 'submitted', 'returned', 'completed', 'not_evaluated') DEFAULT 'draft'");
            
            // Modifier evaluation_status pour ajouter 'not_evaluated'
            DB::statement("ALTER TABLE user_campaigns MODIFY COLUMN evaluation_status ENUM('pending', 'supervisor_draft', 'submitted_to_employee', 'returned_to_supervisor', 'validated', 'not_evaluated') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_campaigns', function (Blueprint $table) {
            // Retirer 'not_evaluated' de objective_status
            DB::statement("ALTER TABLE user_campaigns MODIFY COLUMN objective_status ENUM('draft', 'submitted', 'returned', 'completed') DEFAULT 'draft'");
            
            // Retirer 'not_evaluated' de evaluation_status
            DB::statement("ALTER TABLE user_campaigns MODIFY COLUMN evaluation_status ENUM('pending', 'supervisor_draft', 'submitted_to_employee', 'returned_to_supervisor', 'validated') DEFAULT 'pending'");
        });
    }
};
