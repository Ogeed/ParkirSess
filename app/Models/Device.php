<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'name', 'firmware_version', 'ip_address',
        'wifi_rssi', 'battery_level', 'uptime_seconds',
        'api_token', 'is_online', 'last_seen',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen' => 'datetime',
        'wifi_rssi' => 'integer',
        'battery_level' => 'integer',
        'uptime_seconds' => 'integer',
    ];

    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class, 'device_id', 'id');
    }

    public function sensorAlerts(): HasMany
    {
        return $this->hasMany(SensorAlert::class, 'device_id', 'id');
    }
}
