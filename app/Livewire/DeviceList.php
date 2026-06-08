<?php

namespace App\Livewire;

use App\Models\Device;
use App\Services\DeviceOnlineService;
use Livewire\Component;

class DeviceList extends Component
{
    public $devices = [];
    public string $newName = '';
    public ?string $editingId = null;

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
        $this->refresh();
        return view('livewire.device-list');
    }
}
