<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SensorReading;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SensorReading::with('device');

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }
        if ($request->filled('status')) {
            $query->where('overall_status', $request->status);
        }
        if ($request->filled('sensor_position')) {
            $position = $request->sensor_position;
            if (in_array($position, ['left', 'right', 'back'])) {
                $query->where('status_' . $position, $request->filled('status') ? $request->status : '!=', '');
            }
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        // CSV Export
        if ($request->query('export') === 'csv') {
            return $this->exportCsv($query);
        }

        $perPage = $request->input('per_page', 25);
        $readings = $query->latest()->paginate($perPage)->withQueryString();
        $devices = \App\Models\Device::all();

        return view('dashboard.history', compact('readings', 'devices'));
    }

    private function exportCsv($query)
    {
        $readings = $query->orderBy('created_at', 'desc')->limit(10000)->get();

        $filename = 'sensor-readings-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($readings) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($handle, [
                'Waktu', 'Device ID', 'Kiri (cm)', 'Status Kiri',
                'Kanan (cm)', 'Status Kanan', 'Belakang (cm)',
                'Status Belakang', 'Overall Status', 'WiFi RSSI',
            ]);

            foreach ($readings as $r) {
                fputcsv($handle, [
                    $r->created_at->format('Y-m-d H:i:s'),
                    $r->device_id,
                    number_format($r->sensor_left, 2),
                    $r->status_left,
                    number_format($r->sensor_right, 2),
                    $r->status_right,
                    number_format($r->sensor_back, 2),
                    $r->status_back,
                    $r->overall_status,
                    $r->wifi_rssi ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
