<?php

namespace App\Livewire;

use App\Models\Device;
use App\Models\SensorReading;
use Livewire\Component;

class SensorStatusCard extends Component
{
    public ?SensorReading $latestReading = null;
    public ?Device $device = null;

    public function refresh()
    {
        $this->latestReading = SensorReading::with('device')->latest()->first();
        if ($this->latestReading) {
            $this->device = Device::find($this->latestReading->device_id);
        }
    }

    public function render()
    {
        return view('livewire.sensor-status-card');
    }
}
