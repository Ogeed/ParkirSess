<?php

namespace App\Livewire;

use App\Models\SensorReading;
use Livewire\Component;

class RecentReadings extends Component
{
    public $readings = [];

    public function refresh()
    {
        $this->readings = SensorReading::with('device')
            ->latest()
            ->take(10)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.recent-readings');
    }
}
