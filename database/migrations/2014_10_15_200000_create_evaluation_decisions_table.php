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
        Schema::create('evaluation_decisions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_campaign_uuid');
            $table->foreign('user_campaign_uuid')->references('uuid')->on('user_campaigns')->onDelete('cascade');
            $table->uuid('actor_uuid');
            $table->foreign('actor_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->enum('action', ['submitted_to_employee', 'returned_to_supervisor', 'validated']);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_decisions');
    }
};
