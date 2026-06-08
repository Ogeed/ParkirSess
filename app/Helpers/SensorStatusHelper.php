<?php

namespace App\Helpers;

class SensorStatusHelper
{
    public static function calculateStatus(float $distance): string
    {
        if ($distance > config('sensor.threshold_safe')) return 'SAFE';
        if ($distance >= config('sensor.threshold_warning')) return 'WARNING';
        return 'DANGER';
    }

    public static function calculateOverallStatus(string $left, string $right, string $back): string
    {
        $priority = ['DANGER' => 3, 'WARNING' => 2, 'SAFE' => 1];
        $worst = max($priority[$left] ?? 1, $priority[$right] ?? 1, $priority[$back] ?? 1);
        return array_search($worst, $priority);
    }

    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'SAFE' => '#22C55E',
            'WARNING' => '#EAB308',
            'DANGER' => '#EF4444',
            default => '#94A3B8',
        };
    }

    public static function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'SAFE' => 'bg-green-500/20 text-green-400 border-green-500/30',
            'WARNING' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
            'DANGER' => 'bg-red-500/20 text-red-400 border-red-500/30',
            default => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
        };
    }
}
