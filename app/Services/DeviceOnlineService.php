<?php

namespace App\Services;

use App\Models\Device;
use Carbon\Carbon;

class DeviceOnlineService
{
    public function checkOnlineStatus(Device $device): bool
    {
        if (!$device->last_seen) return false;
        return $device->last_seen->diffInSeconds(now()) < config('sensor.online_timeout_seconds', 30);
    }

    public function getOfflineDuration(Device $device): ?int
    {
        if (!$device->last_seen) return null;
        return $device->last_seen->diffInSeconds(now());
    }

    public function updateAllOnlineStatuses(): int
    {
        $updated = 0;
        Device::chunk(100, function ($devices) use (&$updated) {
            foreach ($devices as $device) {
                $online = $this->checkOnlineStatus($device);
                if ($device->is_online !== $online) {
                    $device->is_online = $online;
                    $device->save();
                    $updated++;
                }
            }
        });
        return $updated;
    }
}
