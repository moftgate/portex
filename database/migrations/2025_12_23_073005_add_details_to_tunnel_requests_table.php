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
        Schema::table('tunnel_requests', function (Blueprint $table) {
            $table->jsonb('request_headers')->nullable()->after('path');
            $table->longText('request_body')->nullable()->after('request_headers');
            $table->jsonb('response_headers')->nullable()->after('status_code');
            $table->longText('response_body')->nullable()->after('response_headers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunnel_requests', function (Blueprint $table) {
            $table->dropColumn([
                'request_headers',
                'request_body',
                'response_headers',
                'response_body'
            ]);
        });
    }
};
