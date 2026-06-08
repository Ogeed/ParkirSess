@extends('layouts.app')

@section('title', 'Dashboard — SmartPark IoT')

@section('content')
<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    @livewire('sensor-status-card', key('sensor-status-card'))
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
    <div class="lg:col-span-2">
        @livewire('live-chart', key('live-chart'))
    </div>
    <div>
        @livewire('device-status', key('device-status'))
    </div>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-2">
    @livewire('car-top-view', key('car-top-view'))
    @livewire('recent-readings', key('recent-readings'))
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('chartUpdated', (data) => {
            if (window.sensorChart) {
                window.sensorChart.data.labels = data.labels;
                window.sensorChart.data.datasets[0].data = data.left;
                window.sensorChart.data.datasets[1].data = data.right;
                window.sensorChart.data.datasets[2].data = data.back;
                window.sensorChart.update('none');
            }
        });
    });
</script>
@endpush
