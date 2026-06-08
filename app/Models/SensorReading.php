<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'device_id', 'sensor_left', 'sensor_right', 'sensor_back',
        'status_left', 'status_right', 'status_back', 'overall_status',
        'wifi_rssi', 'created_at',
    ];

    protected $casts = [
        'sensor_left' => 'decimal:2',
        'sensor_right' => 'decimal:2',
        'sensor_back' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    protected static function booted(): void
    {
        static::creating(function ($reading) {
            if (empty($reading->created_at)) {
                $reading->created_at = now();
            }
        });
    }
}
