<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'id' => 'ESP32-' . strtoupper(fake()->bothify('????-####')),
            'name' => fake()->words(2, true),
            'firmware_version' => '1.0.0',
            'ip_address' => fake()->ipv4(),
            'wifi_rssi' => fake()->numberBetween(-90, -30),
            'api_token' => hash('sha256', fake()->sha256()),
            'is_online' => true,
            'last_seen' => now(),
        ];
    }
}
