<?php

namespace Tests\Unit;

use App\Helpers\SensorStatusHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SensorStatusHelperTest extends TestCase
{
    #[DataProvider('statusDataProvider')]
    public function test_calculate_status(float $distance, string $expected): void
    {
        $this->assertEquals($expected, SensorStatusHelper::calculateStatus($distance));
    }

    public static function statusDataProvider(): array
    {
        return [
            'safe above 50' => [100, 'SAFE'],
            'safe at 51' => [51, 'SAFE'],
            'safe at exactly 50' => [50, 'SAFE'],
            'warning at 30' => [30, 'WARNING'],
            'warning at 20' => [20, 'WARNING'],
            'danger at 19' => [19, 'DANGER'],
            'danger at 10' => [10, 'DANGER'],
            'danger at 0' => [0, 'DANGER'],
            'danger at 2' => [2, 'DANGER'],
        ];
    }

    #[DataProvider('overallStatusDataProvider')]
    public function test_calculate_overall_status(string $left, string $right, string $back, string $expected): void
    {
        $this->assertEquals($expected, SensorStatusHelper::calculateOverallStatus($left, $right, $back));
    }

    public static function overallStatusDataProvider(): array
    {
        return [
            'all safe' => ['SAFE', 'SAFE', 'SAFE', 'SAFE'],
            'one warning' => ['SAFE', 'WARNING', 'SAFE', 'WARNING'],
            'one danger' => ['DANGER', 'SAFE', 'SAFE', 'DANGER'],
            'all danger' => ['DANGER', 'DANGER', 'DANGER', 'DANGER'],
            'warning and danger' => ['WARNING', 'DANGER', 'SAFE', 'DANGER'],
            'mixed safe warning' => ['SAFE', 'WARNING', 'WARNING', 'WARNING'],
        ];
    }

    #[DataProvider('statusColorDataProvider')]
    public function test_get_status_color(string $status, string $expected): void
    {
        $this->assertEquals($expected, SensorStatusHelper::getStatusColor($status));
    }

    public static function statusColorDataProvider(): array
    {
        return [
            'SAFE returns green' => ['SAFE', '#22C55E'],
            'WARNING returns yellow' => ['WARNING', '#EAB308'],
            'DANGER returns red' => ['DANGER', '#EF4444'],
            'unknown returns gray' => ['UNKNOWN', '#94A3B8'],
            'empty returns gray' => ['', '#94A3B8'],
        ];
    }
}
