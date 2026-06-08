<?php

namespace App\Livewire;

use App\Models\Device;
use App\Services\DeviceOnlineService;
use Livewire\Component;

class DeviceStatus extends Component
{
    public $devices = [];

    public function refresh()
    {
        $service = app(DeviceOnlineService::class);
        $this->devices = Device::all()->map(function ($device) use ($service) {
            $device->is_online = $service->checkOnlineStatus($device);
            return $device;
        })->toArray();
    }

    public function render()
    {
        return view('livewire.device-status');
    }
}
