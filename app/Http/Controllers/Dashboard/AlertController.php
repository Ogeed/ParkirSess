<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SensorAlert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $query = SensorAlert::with('device');

        if ($request->filled('alert_type')) {
            $query->where('alert_type', $request->alert_type);
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        $alerts = $query->latest()->paginate(25)->withQueryString();
        $alertTypes = ['DANGER', 'DEVICE_OFFLINE', 'DEVICE_ONLINE'];

        return view('dashboard.alerts', compact('alerts', 'alertTypes'));
    }

    public function acknowledge(string $id)
    {
        $alert = SensorAlert::findOrFail($id);
        $alert->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Alert ditandai');
    }

    public function acknowledgeAll()
    {
        SensorAlert::where('is_acknowledged', false)->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Semua alert ditandai');
    }
}
