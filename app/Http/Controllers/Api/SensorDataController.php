<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\AlertService;
use App\Services\SensorStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SensorDataController extends Controller
{
    public function __construct(
        protected SensorStatusService $sensorService,
        protected AlertService $alertService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string|exists:devices,id',
            'sensor_left' => 'required|numeric|min:0|max:400',
            'sensor_right' => 'required|numeric|min:0|max:400',
            'sensor_back' => 'required|numeric|min:0|max:400',
            'wifi_rssi' => 'nullable|integer|min:-100|max:0',
            'firmware_version' => 'nullable|string|max:20',
            'battery_level' => 'nullable|integer|min:0|max:100',
            'timestamp' => 'nullable|date',
        ]);

        if (RateLimiter::tooManyAttempts('sensor:' . ($validated['device_id'] ?? $request->ip()), 60)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests',
            ], 429);
        }

        RateLimiter::hit('sensor:' . ($validated['device_id'] ?? $request->ip()));

        $reading = $this->sensorService->processReading($validated);

        $this->alertService->checkAndCreateAlerts($reading);

        return response()->json([
            'success' => true,
            'message' => 'Data sensor berhasil disimpan',
            'data' => $reading->toArray(),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $readings = $this->sensorService->getReadingsPaginated($request->all());

        return response()->json([
            'success' => true,
            'data' => $readings,
            'meta' => [
                'total' => count($readings),
            ],
        ]);
    }

    public function latest(Request $request): JsonResponse
    {
        $deviceId = $request->query('device_id');
        $reading = $this->sensorService->getLatestReading($deviceId);

        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'No data available',
            ], 404);
        }

        $device = Device::find($reading->device_id);

        return response()->json([
            'success' => true,
            'data' => $reading->toArray(),
            'device' => $device ? [
                'id' => $device->id,
                'name' => $device->name,
                'is_online' => $device->is_online,
                'last_seen' => $device->last_seen,
                'battery_level' => $device->battery_level,
                'wifi_rssi' => $device->wifi_rssi,
                'firmware_version' => $device->firmware_version,
                'ip_address' => $device->ip_address,
            ] : null,
        ]);
    }

    public function chart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'minutes' => 'nullable|integer|min:1|max:60',
        ]);

        $chartData = $this->sensorService->getChartData(
            $validated['device_id'],
            $validated['minutes'] ?? 10
        );

        return response()->json([
            'success' => true,
            'data' => $chartData,
        ]);
    }
}
