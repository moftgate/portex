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
            $table->string('pin', 4)->nullable()->after('local_port');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunnels', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }
};
