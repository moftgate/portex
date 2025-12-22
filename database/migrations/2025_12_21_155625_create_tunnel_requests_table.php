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
        Schema::create('tunnel_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tunnel_id')->constrained()->onDelete('cascade');
            $table->string('method');
            $table->text('path');
            $table->integer('status_code');
            $table->integer('response_time_ms');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at');

            $table->index('tunnel_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunnel_requests');
    }
};
