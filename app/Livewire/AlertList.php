<?php

namespace App\Livewire;

use App\Models\SensorAlert;
use Livewire\Component;
use Livewire\WithPagination;

class AlertList extends Component
{
    use WithPagination;

    public string $alertType = '';
    public string $from = '';
    public string $to = '';

    protected $queryString = ['alertType', 'from', 'to'];

    public function render()
    {
        $query = SensorAlert::with('device');

        if ($this->alertType) {
            $query->where('alert_type', $this->alertType);
        }
        if ($this->from) {
            $query->where('created_at', '>=', $this->from);
        }
        if ($this->to) {
            $query->where('created_at', '<=', $this->to . ' 23:59:59');
        }

        $alerts = $query->latest()->paginate(25);
        $alertTypes = ['DANGER', 'DEVICE_OFFLINE', 'DEVICE_ONLINE'];

        return view('livewire.alert-list', compact('alerts', 'alertTypes'));
    }

    public function acknowledge(string $id)
    {
        $alert = SensorAlert::findOrFail($id);
        $alert->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);
    }

    public function acknowledgeAll()
    {
        SensorAlert::where('is_acknowledged', false)->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);
    }
}
