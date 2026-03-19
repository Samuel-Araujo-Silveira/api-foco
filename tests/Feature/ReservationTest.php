<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Rate;
use App\Models\ReservationRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private function makeReservationPayload(array $overrides = []): array
    {
        $hotel = Hotel::factory()->create();
        $room  = Room::factory()->create(['hotel_id' => $hotel->id]);
        $rate  = Rate::factory()->create(['hotel_id' => $hotel->id]);

        return array_merge([
            'id'                  => 9999999999,
            'reservation_room_id' => 8888888888,
            'first_name'          => 'João',
            'last_name'           => 'Silva',
            'hotel_id'            => $hotel->id,
            'room_id'             => $room->id,
            'arrival_date'        => '2026-06-01',
            'departure_date'      => '2026-06-05',
            'currencycode'        => 'BRL',
            'meal_plan'           => 'Breakfast included.',
            'totalprice'          => 300.00,
            'guest_counts'        => [
                ['type' => 'adult', 'count' => 2]
            ],
            'prices'              => [
                ['rate_id' => $rate->id, 'date' => '2026-06-01', 'amount' => 150.00],
                ['rate_id' => $rate->id, 'date' => '2026-06-02', 'amount' => 150.00],
            ],
        ], $overrides);
    }

    public function test_can_create_reservation(): void
    {
        $payload = $this->makeReservationPayload();

        $response = $this->postJson('/api/v1/reservations', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', ['id' => 9999999999]);
    }

    public function test_cannot_create_reservation_when_room_is_unavailable(): void
    {
        $payload = $this->makeReservationPayload();

        // primeira reserva
        $this->postJson('/api/v1/reservations', $payload);

        // segunda reserva com datas conflitantes
        $conflictPayload = array_merge($payload, [
            'id'                  => 7777777777,
            'reservation_room_id' => 6666666666,
            'arrival_date'        => '2026-06-03',
            'departure_date'      => '2026-06-07',
        ]);

        $response = $this->postJson('/api/v1/reservations', $conflictPayload);

        $response->assertStatus(409);
    }

    public function test_cannot_create_reservation_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/reservations', []);

        $response->assertStatus(422);
    }
}