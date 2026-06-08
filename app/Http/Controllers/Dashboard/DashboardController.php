<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SensorReading;
use App\Services\SensorStatusService;

class DashboardController extends Controller
{
    public function __construct(
        protected SensorStatusService $sensorService,
    ) {}

    public function index()
    {
        $latestReading = SensorReading::with('device')->latest()->first();
        $devices = Device::all();
        $recentReadings = SensorReading::with('device')->latest()->take(10)->get();

        return view('dashboard.index', compact('latestReading', 'devices', 'recentReadings'));
    }
}
