<?php

namespace App\Services;

use App\Models\Device;
use App\Models\SensorAlert;
use App\Models\SensorReading;

class AlertService
{
    public function checkAndCreateAlerts(SensorReading $reading): void
    {
        if ($reading->status_left === 'DANGER') {
            $this->createAlert($reading->device_id, $reading->id, 'DANGER', 'LEFT', $reading->sensor_left);
        }
        if ($reading->status_right === 'DANGER') {
            $this->createAlert($reading->device_id, $reading->id, 'DANGER', 'RIGHT', $reading->sensor_right);
        }
        if ($reading->status_back === 'DANGER') {
            $this->createAlert($reading->device_id, $reading->id, 'DANGER', 'BACK', $reading->sensor_back);
        }
    }

    public function createDeviceOfflineAlert(Device $device): void
    {
        $exists = SensorAlert::where('device_id', $device->id)
            ->where('alert_type', 'DEVICE_OFFLINE')
            ->where('is_acknowledged', false)
            ->exists();

        if (!$exists) {
            SensorAlert::create([
                'device_id' => $device->id,
                'alert_type' => 'DEVICE_OFFLINE',
            ]);
        }
    }

    public function createDeviceOnlineAlert(Device $device): void
    {
        SensorAlert::create([
            'device_id' => $device->id,
            'alert_type' => 'DEVICE_ONLINE',
        ]);
    }

    private function createAlert(string $deviceId, string $readingId, string $type, string $position, float $distance): void
    {
        $exists = SensorAlert::where('device_id', $deviceId)
            ->where('alert_type', $type)
            ->where('sensor_position', $position)
            ->where('is_acknowledged', false)
            ->exists();

        if (!$exists) {
            SensorAlert::create([
                'device_id' => $deviceId,
                'reading_id' => $readingId,
                'alert_type' => $type,
                'sensor_position' => $position,
                'distance_value' => $distance,
            ]);
        }
    }

    public function getAlertsPaginated(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = SensorAlert::with('device');

        if (!empty($filters['alert_type'])) {
            $query->where('alert_type', $filters['alert_type']);
        }
        if (!empty($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 25);
    }
}
