<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorAlert extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'device_id', 'reading_id', 'alert_type', 'sensor_position',
        'distance_value', 'is_acknowledged', 'acknowledged_at', 'created_at',
    ];

    protected $casts = [
        'is_acknowledged' => 'boolean',
        'distance_value' => 'decimal:2',
        'acknowledged_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    protected static function booted(): void
    {
        static::creating(function ($alert) {
            if (empty($alert->created_at)) {
                $alert->created_at = now();
            }
        });
    }
}
