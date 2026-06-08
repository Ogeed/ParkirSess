<?php

namespace App\Console\Commands;

use App\Models\SensorAlert;
use App\Models\SensorReading;
use Illuminate\Console\Command;

class CleanupOldData extends Command
{
    protected $signature = 'sensor:cleanup {--days=30 : Hapus data lebih dari N hari}';
    protected $description = 'Hapus data sensor dan alert yang sudah lama';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $this->info("Cleaning up data older than {$days} days...");

        $deletedReadings = SensorReading::where('created_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deletedReadings} old sensor readings.");

        $deletedAlerts = SensorAlert::where('created_at', '<', $cutoff)
            ->where('is_acknowledged', true)
            ->delete();
        $this->info("Deleted {$deletedAlerts} old acknowledged alerts.");

        return Command::SUCCESS;
    }
}
