<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\AlertService;
use App\Services\DeviceOnlineService;
use Illuminate\Console\Command;

class CheckDeviceStatus extends Command
{
    protected $signature = 'sensor:check-devices';
    protected $description = 'Check all devices online status and create alerts for offline/online transitions';

    public function handle(DeviceOnlineService $onlineService, AlertService $alertService): int
    {
        $this->info('Checking device statuses...');

        $checked = 0;
        $alertsCreated = 0;

        Device::chunk(100, function ($devices) use ($onlineService, $alertService, &$checked, &$alertsCreated) {
            foreach ($devices as $device) {
                $wasOnline = $device->is_online;
                $isOnline = $onlineService->checkOnlineStatus($device);

                if ($wasOnline !== $isOnline) {
                    $device->is_online = $isOnline;
                    $device->save();

                    if (!$isOnline && $wasOnline) {
                        $alertService->createDeviceOfflineAlert($device);
                        $this->warn("  [OFFLINE] {$device->id}");
                    } elseif ($isOnline && !$wasOnline) {
                        $alertService->createDeviceOnlineAlert($device);
                        $this->info("  [ONLINE] {$device->id}");
                    }

                    $alertsCreated++;
                }

                $checked++;
            }
        });

        $this->info("Checked {$checked} devices, created {$alertsCreated} alerts.");

        return Command::SUCCESS;
    }
}
