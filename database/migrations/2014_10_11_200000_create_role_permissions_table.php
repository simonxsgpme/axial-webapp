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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('role_uuid');
            $table->uuid('permission_uuid');
            $table->boolean('status')->default(false);
            $table->timestamps();

            $table->foreign('role_uuid')->references('uuid')->on('roles')->onDelete('cascade');
            $table->foreign('permission_uuid')->references('uuid')->on('permissions')->onDelete('cascade');
            $table->unique(['role_uuid', 'permission_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
