<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SensorDataApiTest extends TestCase
{
    use RefreshDatabase;

    private Device $device;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = 'test-token-12345';
        $this->device = Device::factory()->create([
            'api_token' => hash('sha256', $this->token),
            'is_online' => false,
        ]);
    }

    public function test_esp32_can_submit_sensor_data(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/sensor-data', [
            'device_id' => $this->device->id,
            'sensor_left' => 45.2,
            'sensor_right' => 30.8,
            'sensor_back' => 15.3,
            'wifi_rssi' => -65,
            'firmware_version' => '1.0.0',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Data sensor berhasil disimpan',
            ]);

        $this->assertDatabaseHas('sensor_readings', [
            'device_id' => $this->device->id,
            'sensor_left' => 45.2,
            'sensor_right' => 30.8,
            'sensor_back' => 15.3,
            'status_left' => 'SAFE',
            'status_right' => 'WARNING',
            'status_back' => 'DANGER',
            'overall_status' => 'DANGER',
        ]);
    }

    public function test_invalid_token_returns_401(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/sensor-data', [
            'device_id' => $this->device->id,
            'sensor_left' => 45.2,
            'sensor_right' => 30.8,
            'sensor_back' => 15.3,
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized - Invalid token',
            ]);
    }

    public function test_missing_token_returns_401(): void
    {
        $response = $this->postJson('/api/v1/sensor-data', [
            'device_id' => $this->device->id,
            'sensor_left' => 45.2,
            'sensor_right' => 30.8,
            'sensor_back' => 15.3,
        ]);

        $response->assertStatus(401);
    }

    public function test_out_of_range_distance_returns_422(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/sensor-data', [
            'device_id' => $this->device->id,
            'sensor_left' => 500,
            'sensor_right' => 30.8,
            'sensor_back' => 15.3,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sensor_left']);
    }

    public function test_overall_status_calculated_correctly(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/sensor-data', [
            'device_id' => $this->device->id,
            'sensor_left' => 10,
            'sensor_right' => 30,
            'sensor_back' => 60,
        ]);

        $response->assertStatus(201);
        $data = $response->json('data');
        $this->assertEquals('DANGER', $data['status_left']);
        $this->assertEquals('WARNING', $data['status_right']);
        $this->assertEquals('SAFE', $data['status_back']);
        $this->assertEquals('DANGER', $data['overall_status']);
    }

    public function test_device_registration_works(): void
    {
        $token = 'new-device-token';
        $deviceData = [
            'device_id' => 'ESP32-TEST-002',
            'name' => 'Test Device',
            'firmware_version' => '1.0.0',
            'ip_address' => '192.168.1.100',
            'battery_level' => 85,
            'uptime_seconds' => 3600,
        ];

        $devicesBefore = Device::count();
        $expectedToken = 'new-device-token'; // dummy, the middleware won't check on register
        // Note: register endpoint needs ESP token, so we'll simulate with non-existing token

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/devices/register', $deviceData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Perangkat terdaftar',
            ]);

        $this->assertDatabaseHas('devices', [
            'id' => 'ESP32-TEST-002',
            'name' => 'Test Device',
            'firmware_version' => '1.0.0',
            'ip_address' => '192.168.1.100',
            'battery_level' => 85,
            'uptime_seconds' => 3600,
        ]);
    }

    public function test_latest_endpoint_returns_correct_data(): void
    {
        SensorReading::factory()->create([
            'device_id' => $this->device->id,
            'sensor_left' => 10,
            'sensor_back' => 15,
        ]);

        $response = $this->getJson('/api/v1/sensor-data/latest?device_id=' . $this->device->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_chart_endpoint_returns_data(): void
    {
        SensorReading::factory()->count(5)->create([
            'device_id' => $this->device->id,
        ]);

        $response = $this->getJson('/api/v1/sensor-data/chart?device_id=' . $this->device->id . '&minutes=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'labels',
                    'datasets' => ['left', 'right', 'back'],
                ],
            ]);
    }
}
