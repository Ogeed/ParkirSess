<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->integer('battery_level')->nullable()->after('wifi_rssi');
            $table->integer('uptime_seconds')->nullable()->after('battery_level');
            $table->softDeletesTz();
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['battery_level', 'uptime_seconds']);
            $table->dropSoftDeletesTz();
        });
    }
};
