<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\DeviceOnlineService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceOnlineService $onlineService,
    ) {}

    public function index()
    {
        $devices = Device::all();

        foreach ($devices as $device) {
            $device->is_online = $this->onlineService->checkOnlineStatus($device);
        }

        return view('dashboard.devices', compact('devices'));
    }

    public function update(Request $request, string $id)
    {
        $device = Device::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $device->update($validated);

        return redirect()->route('dashboard.devices')->with('success', 'Perangkat diperbarui');
    }

    public function destroy(string $id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->route('dashboard.devices')->with('success', 'Perangkat dihapus');
    }

    public function resetToken(string $id)
    {
        $device = Device::findOrFail($id);
        $token = Str::random(64);
        $device->update(['api_token' => hash('sha256', $token)]);

        return redirect()->route('dashboard.devices')->with('success', 'Token berhasil direset. Token baru: ' . $token);
    }
}
