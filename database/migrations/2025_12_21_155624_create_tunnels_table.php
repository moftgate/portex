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
        Schema::create('tunnels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('agent_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->integer('local_port');
            $table->enum('protocol', ['http', 'https', 'tcp'])->default('http');
            $table->enum('status', ['active', 'inactive', 'error'])->default('inactive');
            $table->string('custom_domain')->nullable();
            $table->boolean('auth_enabled')->default(false);
            $table->string('auth_username')->nullable();
            $table->string('auth_password')->nullable();
            $table->integer('max_connections')->default(100);
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->index('subdomain');
            $table->index('status');
            $table->index('custom_domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunnels');
    }
};
