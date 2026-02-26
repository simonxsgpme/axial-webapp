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
        Schema::create('user_campaigns', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid');
            $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->uuid('campaign_uuid');
            $table->foreign('campaign_uuid')->references('uuid')->on('campaigns')->onDelete('cascade');
            $table->uuid('supervisor_uuid')->nullable();
            $table->foreign('supervisor_uuid')->references('uuid')->on('users')->onDelete('set null');
            $table->enum('objective_status', ['draft', 'submitted', 'returned', 'completed'])->default('draft');
            $table->enum('evaluation_status', ['pending', 'supervisor_draft', 'submitted_to_employee', 'returned_to_supervisor', 'validated'])->default('pending');
            $table->decimal('rating', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['user_uuid', 'campaign_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_campaigns');
    }
};
