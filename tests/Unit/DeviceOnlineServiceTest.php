<?php

namespace Tests\Unit;

use App\Models\Device;
use App\Services\DeviceOnlineService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceOnlineServiceTest extends TestCase
{
    use RefreshDatabase;

    private DeviceOnlineService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DeviceOnlineService::class);
    }

    public function test_device_with_recent_last_seen_is_online(): void
    {
        $device = Device::factory()->create([
            'last_seen' => now()->subSeconds(10),
        ]);

        $this->assertTrue($this->service->checkOnlineStatus($device));
    }

    public function test_device_with_old_last_seen_is_offline(): void
    {
        $device = Device::factory()->create([
            'last_seen' => now()->subSeconds(60),
        ]);

        $this->assertFalse($this->service->checkOnlineStatus($device));
    }

    public function test_device_without_last_seen_is_offline(): void
    {
        $device = Device::factory()->create([
            'last_seen' => null,
        ]);

        $this->assertFalse($this->service->checkOnlineStatus($device));
    }

    public function test_device_at_exact_timeout_boundary_is_online(): void
    {
        $device = Device::factory()->create([
            'last_seen' => now()->subSeconds(config('sensor.online_timeout_seconds', 30)),
        ]);

        $this->assertTrue($this->service->checkOnlineStatus($device));
    }

    public function test_get_offline_duration_returns_correct_seconds(): void
    {
        Carbon::setTestNow(now());

        $device = Device::factory()->create([
            'last_seen' => now()->subMinutes(5),
        ]);

        $duration = $this->service->getOfflineDuration($device);
        $this->assertEquals(300, $duration);

        Carbon::setTestNow();
    }

    public function test_update_all_online_statuses_updates_devices(): void
    {
        Device::factory()->create([
            'is_online' => true,
            'last_seen' => now()->subMinutes(5),
        ]);
        Device::factory()->create([
            'is_online' => true,
            'last_seen' => now()->subSeconds(10),
        ]);

        $updated = $this->service->updateAllOnlineStatuses();

        $this->assertEquals(1, $updated);
        $this->assertFalse(Device::first()->is_online);
        $this->assertTrue(Device::skip(0)->first()->is_online ?? true);
    }
}
