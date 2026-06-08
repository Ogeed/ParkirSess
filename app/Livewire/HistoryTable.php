<?php

namespace App\Livewire;

use App\Models\SensorReading;
use Livewire\Component;
use Livewire\WithPagination;

class HistoryTable extends Component
{
    use WithPagination;

    public string $deviceId = '';
    public string $status = '';
    public string $sensorPosition = '';
    public string $from = '';
    public string $to = '';
    public int $perPage = 25;

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public ?string $detailReadingId = null;

    protected $queryString = [
        'deviceId', 'status', 'sensorPosition', 'from', 'to',
        'perPage', 'sortField', 'sortDirection',
    ];

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function showDetail(string $id)
    {
        $this->detailReadingId = $id;
    }

    public function closeDetail()
    {
        $this->detailReadingId = null;
    }

    public function render()
    {
        $query = SensorReading::with('device');

        if ($this->deviceId) {
            $query->where('device_id', $this->deviceId);
        }
        if ($this->status) {
            $query->where('overall_status', $this->status);
        }
        if ($this->sensorPosition === 'left') {
            $query->where('status_left', $this->status ?: '!=', '');
        }
        if ($this->sensorPosition === 'right') {
            $query->where('status_right', $this->status ?: '!=', '');
        }
        if ($this->sensorPosition === 'back') {
            $query->where('status_back', $this->status ?: '!=', '');
        }
        if ($this->from) {
            $query->where('created_at', '>=', $this->from);
        }
        if ($this->to) {
            $query->where('created_at', '<=', $this->to . ' 23:59:59');
        }

        $readings = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
        $devices = \App\Models\Device::all();

        $detailReading = null;
        if ($this->detailReadingId) {
            $detailReading = SensorReading::with('device')->find($this->detailReadingId);
        }

        return view('livewire.history-table', compact('readings', 'devices', 'detailReading'));
    }

    public function resetFilters()
    {
        $this->reset(['deviceId', 'status', 'sensorPosition', 'from', 'to', 'sortField', 'sortDirection']);
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
    }
}
