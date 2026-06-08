<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\SensorReading;
use App\Helpers\SensorStatusHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class SensorReadingFactory extends Factory
{
    protected $model = SensorReading::class;

    public function definition(): array
    {
        $left = fake()->randomFloat(2, 2, 100);
        $right = fake()->randomFloat(2, 2, 100);
        $back = fake()->randomFloat(2, 2, 100);
        $statusLeft = SensorStatusHelper::calculateStatus($left);
        $statusRight = SensorStatusHelper::calculateStatus($right);
        $statusBack = SensorStatusHelper::calculateStatus($back);

        return [
            'device_id' => Device::factory(),
            'sensor_left' => $left,
            'sensor_right' => $right,
            'sensor_back' => $back,
            'status_left' => $statusLeft,
            'status_right' => $statusRight,
            'status_back' => $statusBack,
            'overall_status' => SensorStatusHelper::calculateOverallStatus($statusLeft, $statusRight, $statusBack),
            'wifi_rssi' => fake()->numberBetween(-90, -30),
            'created_at' => fake()->dateTimeBetween('-1 hour', 'now'),
        ];
    }

    public function danger(): static
    {
        return $this->state(fn(array $attributes) => [
            'sensor_left' => 5,
            'sensor_right' => 10,
            'sensor_back' => 3,
            'status_left' => 'DANGER',
            'status_right' => 'DANGER',
            'status_back' => 'DANGER',
            'overall_status' => 'DANGER',
        ]);
    }

    public function safe(): static
    {
        return $this->state(fn(array $attributes) => [
            'sensor_left' => 80,
            'sensor_right' => 90,
            'sensor_back' => 70,
            'status_left' => 'SAFE',
            'status_right' => 'SAFE',
            'status_back' => 'SAFE',
            'overall_status' => 'SAFE',
        ]);
    }
}
