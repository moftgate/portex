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
        Schema::table('agents', function (Blueprint $table) {
            $table->bigInteger('bytes_uploaded')->default(0)->after('status');
            $table->bigInteger('bytes_downloaded')->default(0)->after('bytes_uploaded');
            $table->integer('total_requests')->default(0)->after('bytes_downloaded');
            $table->integer('total_usage_seconds')->default(0)->after('total_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'bytes_uploaded',
                'bytes_downloaded',
                'total_requests',
                'total_usage_seconds'
            ]);
        });
    }
};
