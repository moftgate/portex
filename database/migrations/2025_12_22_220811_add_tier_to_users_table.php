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
        Schema::table('users', function (Blueprint $table) {
            $table->string('tier')->default('free')->after('email_verified_at'); // free, premium
            $table->integer('daily_usage_limit_seconds')->default(3600)->after('tier'); // 1 hour for free
            $table->bigInteger('bandwidth_limit_bytes')->default(1073741824)->after('daily_usage_limit_seconds'); // 1GB for free

            $table->index('tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tier', 'daily_usage_limit_seconds', 'bandwidth_limit_bytes']);
        });
    }
};
