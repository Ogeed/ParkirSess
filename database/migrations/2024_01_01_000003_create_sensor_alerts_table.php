<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('device_id', 50);
            $table->uuid('reading_id')->nullable();
            $table->string('alert_type', 20);
            $table->string('sensor_position', 10)->nullable();
            $table->decimal('distance_value', 6, 2)->nullable();
            $table->boolean('is_acknowledged')->default(false);
            $table->timestampTz('acknowledged_at')->nullable();
            $table->timestampTz('created_at')->nullable();

            $table->foreign('device_id')->references('id')->on('devices');
            $table->foreign('reading_id')->references('id')->on('sensor_readings');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_alerts');
    }
};
