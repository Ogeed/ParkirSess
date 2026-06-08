<?php

namespace App\Services;

use App\Helpers\SensorStatusHelper;
use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Support\Collection;

class SensorStatusService
{
    public function processReading(array $data): SensorReading
    {
        $left = round((float) $data['sensor_left'], 2);
        $right = round((float) $data['sensor_right'], 2);
        $back = round((float) $data['sensor_back'], 2);

        $statusLeft = SensorStatusHelper::calculateStatus($left);
        $statusRight = SensorStatusHelper::calculateStatus($right);
        $statusBack = SensorStatusHelper::calculateStatus($back);
        $overall = SensorStatusHelper::calculateOverallStatus($statusLeft, $statusRight, $statusBack);

        $reading = SensorReading::create([
            'device_id' => $data['device_id'],
            'sensor_left' => $left,
            'sensor_right' => $right,
            'sensor_back' => $back,
            'status_left' => $statusLeft,
            'status_right' => $statusRight,
            'status_back' => $statusBack,
            'overall_status' => $overall,
            'wifi_rssi' => $data['wifi_rssi'] ?? null,
        ]);

        $deviceUpdate = [
            'is_online' => true,
            'last_seen' => now(),
            'wifi_rssi' => $data['wifi_rssi'] ?? null,
        ];

        if (isset($data['battery_level'])) {
            $deviceUpdate['battery_level'] = (int) $data['battery_level'];
        }

        Device::where('id', $data['device_id'])->update($deviceUpdate);

        return $reading;
    }

    public function getLatestReading(?string $deviceId = null): ?SensorReading
    {
        $query = SensorReading::query();
        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }
        return $query->latest()->first();
    }

    public function getChartData(string $deviceId, int $minutes = 10): array
    {
        $readings = SensorReading::where('device_id', $deviceId)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at')
            ->get(['sensor_left', 'sensor_right', 'sensor_back', 'created_at']);

        return [
            'labels' => $readings->map(fn($r) => $r->created_at->format('H:i:s')),
            'datasets' => [
                'left' => $readings->pluck('sensor_left'),
                'right' => $readings->pluck('sensor_right'),
                'back' => $readings->pluck('sensor_back'),
            ],
        ];
    }

    public function getReadingsPaginated(array $filters = []): Collection
    {
        $query = SensorReading::with('device');

        if (!empty($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('overall_status', $filters['status']);
        }
        if (!empty($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }

        return $query->latest()->take($filters['limit'] ?? 50)->get();
    }
}
