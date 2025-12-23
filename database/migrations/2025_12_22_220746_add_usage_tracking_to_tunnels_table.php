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
        Schema::table('tunnels', function (Blueprint $table) {
            $table->bigInteger('bytes_uploaded')->default(0)->after('status');
            $table->bigInteger('bytes_downloaded')->default(0)->after('bytes_uploaded');
            $table->integer('total_requests')->default(0)->after('bytes_downloaded');
            $table->timestamp('last_activity_at')->nullable()->after('total_requests');
            $table->integer('usage_seconds_today')->default(0)->after('last_activity_at');
            $table->date('usage_reset_date')->nullable()->after('usage_seconds_today');

            $table->index('last_activity_at');
            $table->index('usage_reset_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunnels', function (Blueprint $table) {
            $table->dropColumn([
                'bytes_uploaded',
                'bytes_downloaded',
                'total_requests',
                'last_activity_at',
                'usage_seconds_today',
                'usage_reset_date',
            ]);
        });
    }
};
