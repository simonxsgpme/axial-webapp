<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objective_histories', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('objective_uuid');
            $table->foreign('objective_uuid')->references('uuid')->on('objectives')->onDelete('cascade');
            $table->uuid('changed_by_uuid');
            $table->foreign('changed_by_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('field');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('phase')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objective_histories');
    }
};
