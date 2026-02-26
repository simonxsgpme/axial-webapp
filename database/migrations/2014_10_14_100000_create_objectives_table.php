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
        Schema::create('objectives', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_campaign_uuid');
            $table->foreign('user_campaign_uuid')->references('uuid')->on('user_campaigns')->onDelete('cascade');
            $table->uuid('objective_category_uuid');
            $table->foreign('objective_category_uuid')->references('uuid')->on('objective_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('weight')->default(0);
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectives');
    }
};
