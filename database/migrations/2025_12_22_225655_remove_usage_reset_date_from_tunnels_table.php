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
            $table->dropColumn('usage_reset_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunnels', function (Blueprint $table) {
            $table->date('usage_reset_date')->nullable()->after('usage_seconds_today')->index();
        });
    }
};
