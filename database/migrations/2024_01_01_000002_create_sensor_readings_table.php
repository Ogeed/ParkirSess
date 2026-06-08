<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('device_id', 50);
            $table->decimal('sensor_left', 6, 2);
            $table->decimal('sensor_right', 6, 2);
            $table->decimal('sensor_back', 6, 2);
            $table->string('status_left', 10);
            $table->string('status_right', 10);
            $table->string('status_back', 10);
            $table->string('overall_status', 10);
            $table->integer('wifi_rssi')->nullable();
            $table->timestampTz('created_at')->nullable();

            $table->foreign('device_id')->references('id')->on('devices');
            $table->index('device_id');
            $table->index('created_at');
            $table->index(['device_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
