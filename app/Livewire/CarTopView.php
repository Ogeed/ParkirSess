<?php

namespace App\Livewire;

use App\Models\SensorReading;
use App\Helpers\SensorStatusHelper;
use Livewire\Component;

class CarTopView extends Component
{
    public ?SensorReading $latestReading = null;

    public function refresh()
    {
        $this->latestReading = SensorReading::latest()->first();
    }

    public function render()
    {
        return view('livewire.car-top-view');
    }
}
