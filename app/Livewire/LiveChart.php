<?php

namespace App\Livewire;

use App\Models\SensorReading;
use Livewire\Component;

class LiveChart extends Component
{
    public array $chartData = [
        'labels' => [],
        'left' => [],
        'right' => [],
        'back' => [],
    ];

    public function refresh()
    {
        $readings = SensorReading::where('created_at', '>=', now()->subSeconds(65))
            ->orderBy('created_at')
            ->get(['sensor_left', 'sensor_right', 'sensor_back', 'created_at']);

        $this->chartData = [
            'labels' => $readings->map(fn($r) => $r->created_at->format('H:i:s'))->toArray(),
            'left' => $readings->pluck('sensor_left')->toArray(),
            'right' => $readings->pluck('sensor_right')->toArray(),
            'back' => $readings->pluck('sensor_back')->toArray(),
        ];

        $this->dispatch('chartUpdated', chartData: $this->chartData);
    }

    public function render()
    {
        return view('livewire.live-chart');
    }
}
