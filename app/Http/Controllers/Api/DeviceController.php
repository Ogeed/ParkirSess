<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\DeviceOnlineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceOnlineService $onlineService,
    ) {}

    public function index(): JsonResponse
    {
        $devices = Device::all(['id', 'name', 'is_online', 'last_seen', 'wifi_rssi', 'firmware_version', 'ip_address']);

        foreach ($devices as $device) {
            $device->is_online = $this->onlineService->checkOnlineStatus($device);
        }

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'firmware_version' => 'nullable|string|max:20',
            'ip_address' => 'nullable|string|max:45',
            'battery_level' => 'nullable|integer|min:0|max:100',
            'uptime_seconds' => 'nullable|integer|min:0',
        ]);

        $token = Str::random(64);
        $hashedToken = hash('sha256', $token);

        $device = Device::updateOrCreate(
            ['id' => $validated['device_id']],
            [
                'name' => $validated['name'],
                'firmware_version' => $validated['firmware_version'] ?? null,
                'ip_address' => $validated['ip_address'] ?? null,
                'battery_level' => $validated['battery_level'] ?? null,
                'uptime_seconds' => $validated['uptime_seconds'] ?? null,
                'api_token' => $hashedToken,
                'is_online' => true,
                'last_seen' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Perangkat terdaftar',
            'api_token' => $token,
        ]);
    }

    public function status(string $id): JsonResponse
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $device->is_online = $this->onlineService->checkOnlineStatus($device);
        $offlineDuration = $this->onlineService->getOfflineDuration($device);

        return response()->json([
            'success' => true,
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'is_online' => $device->is_online,
                'last_seen' => $device->last_seen,
                'uptime_seconds' => $device->is_online ? $device->uptime_seconds : 0,
                'offline_duration_seconds' => $device->is_online ? 0 : $offlineDuration,
                'wifi_rssi' => $device->wifi_rssi,
                'battery_level' => $device->battery_level,
                'firmware_version' => $device->firmware_version,
                'ip_address' => $device->ip_address,
                'created_at' => $device->created_at,
            ],
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
        ]);

        $device->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat diperbarui',
            'device' => $device,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perangkat dihapus',
        ]);
    }
}
