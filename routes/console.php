<?php

use Illuminate\Support\Facades\Schedule;

// Cek status perangkat setiap 30 detik
Schedule::command('sensor:check-devices')->everyThirtySeconds();

// Bersihkan data lama setiap hari jam 03:00
Schedule::command('sensor:cleanup --days=30')->dailyAt('03:00');
