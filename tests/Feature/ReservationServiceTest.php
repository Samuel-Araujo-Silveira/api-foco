<?php
namespace Tests\Feature; // ✅ Feature, não Unit

use Tests\TestCase;
use App\Services\ReservationService;
use App\Models\ReservationRoom;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReservationService();
    }

    public function test_room_is_available_when_no_reservations_exist(): void
    {
        $room = Room::factory()->create();

        $result = $this->service->isRoomAvailable(
            $room->id,
            '2026-06-01',
            '2026-06-05'
        );

        $this->assertTrue($result);
    }

    public function test_room_is_not_available_when_dates_overlap(): void
    {
        $reservationRoom = ReservationRoom::factory()->create([
            'arrival_date'   => '2026-06-01',
            'departure_date' => '2026-06-05',
        ]);

        $result = $this->service->isRoomAvailable(
            $reservationRoom->room_id,
            '2026-06-03',
            '2026-06-07'
        );

        $this->assertFalse($result);
    }

    public function test_room_is_available_when_dates_do_not_overlap(): void
    {
        $reservationRoom = ReservationRoom::factory()->create([
            'arrival_date'   => '2026-06-01',
            'departure_date' => '2026-06-05',
        ]);

        $result = $this->service->isRoomAvailable(
            $reservationRoom->room_id,
            '2026-06-05',
            '2026-06-08'
        );

        $this->assertTrue($result);
    }
}
