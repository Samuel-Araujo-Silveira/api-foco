<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ReservationService; 

class AvailabilityLogicTest extends TestCase
{
    private ReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReservationService();
    }

    public function test_dates_overlap(): void
    {
        $result = $this->service->hasConflict('2026-06-03', '2026-06-07', '2026-06-01', '2026-06-05');
        $this->assertTrue($result);
    }

    public function test_dates_do_not_overlap(): void
    {
        $result = $this->service->hasConflict('2026-06-05', '2026-06-08', '2026-06-01', '2026-06-05');
        $this->assertFalse($result);
    }

    public function test_dates_completely_before(): void
    {
        $result = $this->service->hasConflict('2026-05-01', '2026-05-05', '2026-06-01', '2026-06-05');
        $this->assertFalse($result);
    }

    public function test_dates_completely_after(): void
    {
        $result = $this->service->hasConflict('2026-07-01', '2026-07-05', '2026-06-01', '2026-06-05');
        $this->assertFalse($result);
    }
}