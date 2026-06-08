<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\SensorAlert;
use App\Models\SensorReading;
use App\Services\AlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertServiceTest extends TestCase
{
    use RefreshDatabase;

    private AlertService $service;
    private Device $device;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AlertService::class);
        $this->device = Device::factory()->create();
    }

    public function test_danger_alert_created_for_left_sensor(): void
    {
        $reading = SensorReading::factory()->create([
            'device_id' => $this->device->id,
            'status_left' => 'DANGER',
            'status_right' => 'SAFE',
            'status_back' => 'SAFE',
            'sensor_left' => 10,
        ]);

        $this->service->checkAndCreateAlerts($reading);

        $this->assertDatabaseHas('sensor_alerts', [
            'device_id' => $this->device->id,
            'alert_type' => 'DANGER',
            'sensor_position' => 'LEFT',
            'distance_value' => 10,
            'is_acknowledged' => false,
        ]);
    }

    public function test_no_duplicate_danger_alerts(): void
    {
        $reading = SensorReading::factory()->create([
            'device_id' => $this->device->id,
            'status_left' => 'DANGER',
            'status_right' => 'DANGER',
            'status_back' => 'DANGER',
        ]);

        $this->service->checkAndCreateAlerts($reading);
        $this->service->checkAndCreateAlerts($reading);

        $this->assertEquals(3, SensorAlert::count());
    }

    public function test_device_offline_alert_created(): void
    {
        $this->service->createDeviceOfflineAlert($this->device);

        $this->assertDatabaseHas('sensor_alerts', [
            'device_id' => $this->device->id,
            'alert_type' => 'DEVICE_OFFLINE',
            'is_acknowledged' => false,
        ]);
    }

    public function test_device_online_alert_created(): void
    {
        $this->service->createDeviceOnlineAlert($this->device);

        $this->assertDatabaseHas('sensor_alerts', [
            'device_id' => $this->device->id,
            'alert_type' => 'DEVICE_ONLINE',
        ]);
    }

    public function test_no_duplicate_offline_alert(): void
    {
        $this->service->createDeviceOfflineAlert($this->device);
        $this->service->createDeviceOfflineAlert($this->device);

        $this->assertEquals(1, SensorAlert::where('alert_type', 'DEVICE_OFFLINE')->count());
    }

    public function test_alert_can_be_acknowledged(): void
    {
        $reading = SensorReading::factory()->create([
            'device_id' => $this->device->id,
            'status_left' => 'DANGER',
        ]);

        $this->service->checkAndCreateAlerts($reading);

        $alert = SensorAlert::first();
        $alert->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);

        $this->assertTrue($alert->fresh()->is_acknowledged);
        $this->assertNotNull($alert->fresh()->acknowledged_at);
    }
}
