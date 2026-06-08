<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('name', 100);
            $table->string('firmware_version', 20)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->integer('wifi_rssi')->nullable();
            $table->string('api_token', 255)->unique();
            $table->boolean('is_online')->default(false);
            $table->timestampTz('last_seen')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
