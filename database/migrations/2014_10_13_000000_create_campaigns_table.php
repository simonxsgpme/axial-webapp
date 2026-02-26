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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->year('year');
            $table->date('objective_starts_at')->nullable();
            $table->date('objective_stops_at')->nullable();
            $table->date('evaluation_starts_at')->nullable();
            $table->date('evaluation_stops_at')->nullable();
            $table->enum('status', [
                'draft',
                'objective_in_progress',
                'objective_completed',
                'evaluation_in_progress',
                'evaluation_completed',
                'archived',
            ])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
