<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        return view('dashboard.settings');
    }

    public function updateThreshold(Request $request)
    {
        $validated = $request->validate([
            'threshold_safe' => 'required|numeric|min:30|max:400',
            'threshold_warning' => 'required|numeric|min:5|max:100',
        ]);

        $envContent = file_get_contents(base_path('.env'));

        $envContent = preg_replace(
            '/SENSOR_THRESHOLD_SAFE=.*/',
            'SENSOR_THRESHOLD_SAFE=' . $validated['threshold_safe'],
            $envContent
        );
        $envContent = preg_replace(
            '/SENSOR_THRESHOLD_WARNING=.*/',
            'SENSOR_THRESHOLD_WARNING=' . $validated['threshold_warning'],
            $envContent
        );

        if (!str_contains($envContent, 'SENSOR_THRESHOLD_SAFE')) {
            $envContent .= "\nSENSOR_THRESHOLD_SAFE=" . $validated['threshold_safe'];
        }
        if (!str_contains($envContent, 'SENSOR_THRESHOLD_WARNING')) {
            $envContent .= "\nSENSOR_THRESHOLD_WARNING=" . $validated['threshold_warning'];
        }

        file_put_contents(base_path('.env'), $envContent);

        return redirect()->route('dashboard.settings')->with('success', 'Threshold diperbarui');
    }

    public function updateAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('dashboard.settings')->with('success', 'Akun diperbarui');
    }
}
