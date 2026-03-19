<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\Customer;
use App\Models\ReservationRoom;
use App\Models\GuestCount;
use App\Models\Rate;
use App\Models\Room;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    private function createReservation(): Reservation
    {
        $customer = Customer::factory()->create();

        $reservation = Reservation::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $rate = Rate::factory()->create();

        $room = ReservationRoom::factory()->create([
            'reservation_id' => $reservation->id,
        ]);

        GuestCount::factory()->create([
            'reservation_room_id' => $room->id,
        ]);

        $room->rates()->attach($rate->id, [
            'date'   => now()->toDateString(),
            'amount' => 250.00,
        ]);

        return $reservation;
    }


    private function getValidPayload(array $overrides = []): array
    {
        $hotelId = $overrides['hotel_id'] ?? Hotel::factory()->create()->id;
        $roomId  = $overrides['room_id'] ?? Room::factory()->create(['hotel_id' => $hotelId])->id;
        $rateId  = Rate::factory()->create()->id;

        return array_merge([
            'id'                  => rand(100000, 999999), 
            'reservation_room_id' => rand(100000, 999999), 
            'hotel_id'            => $hotelId,
            'room_id'             => $roomId,
            'first_name'          => 'John',
            'last_name'           => 'Doe',
            'arrival_date'        => now()->addDays(5)->toDateString(),
            'departure_date'      => now()->addDays(10)->toDateString(),
            'currencycode'        => 'USD',
            'meal_plan'           => 'BB',
            'totalprice'          => 1250.00,
            'guest_counts'        => [
                ['type' => 'adult', 'count' => 2],
            ],
            'prices' => [
                [
                    'rate_id' => $rateId,
                    'date'    => now()->addDays(5)->toDateString(),
                    'amount'  => 250.00,
                ],
            ],
        ], $overrides);
    }

    public function test_index_returns_all_reservations(): void
    {
        $this->createReservation();
        $this->createReservation();

        $response = $this->getJson(route('v1.reservations.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'date',
                    'time',
                    'hotel_id',
                    'customer' => ['first_name', 'last_name'],
                    'room'     => [
                        'id',
                        'arrival_date',
                        'departure_date',
                        'currencycode',
                        'meal_plan',
                        'totalprice',
                        'guest_counts',
                        'prices',
                    ],
                ],
            ],
        ]);
    }

    public function test_index_returns_empty_collection_when_no_reservations(): void
    {
        $response = $this->getJson(route('v1.reservations.index'));

        $response->assertStatus(200);

        $response->assertJsonPath('data', []);
    }

    public function test_show_returns_reservation(): void
    {
        $reservation = $this->createReservation();

        $response = $this->getJson(route('v1.reservations.show', $reservation));

        $response->assertStatus(200);

        $response->assertJsonPath('data.id', $reservation->id);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'date',
                'time',
                'hotel_id',
                'customer' => ['first_name', 'last_name'],
                'room'     => [
                    'id',
                    'arrival_date',
                    'departure_date',
                    'currencycode',
                    'meal_plan',
                    'totalprice',
                    'guest_counts',
                    'prices',
                ],
            ],
        ]);
    }

    public function test_show_returns_404_when_reservation_not_found(): void
    {
        $response = $this->getJson(route('v1.reservations.show', 999999));

        $response->assertStatus(404);
    }

    public function test_store_creates_reservation_successfully(): void
    {
        $payload = $this->getValidPayload();

        $response = $this->postJson(route('v1.reservations.store'), $payload);

        $response->assertStatus(201);

        $response->assertJsonPath('message', 'Reservation created');
        $response->assertJsonPath('status', 201);

        $this->assertDatabaseHas('reservations', [
            'id' => $payload['id'],
        ]);
    }

    public function test_store_returns_409_when_room_is_not_available(): void
    {
        $existingRoom = ReservationRoom::factory()->create([
            'arrival_date'   => now()->addDays(3)->toDateString(),
            'departure_date' => now()->addDays(8)->toDateString(),
        ]);

        $payload = $this->getValidPayload([
            'room_id'        => $existingRoom->room_id,
            'arrival_date'   => now()->addDays(5)->toDateString(),  
            'departure_date' => now()->addDays(10)->toDateString(),
        ]);

        $response = $this->postJson(route('v1.reservations.store'), $payload);

        $response->assertStatus(409);

        $response->assertJsonPath('message', 'Room not available for this period');
        $response->assertJsonPath('status', 409);
    }

    public function test_store_returns_422_when_payload_is_invalid(): void
    {
        $response = $this->postJson(route('v1.reservations.store'), []);

        $response->assertStatus(422);

        $response->assertJsonStructure(['errors']);
    }

    public function test_destroy_deletes_reservation_successfully(): void
    {
        $reservation = $this->createReservation();

        $response = $this->deleteJson(route('v1.reservations.destroy', $reservation));

        $response->assertStatus(200);

        $response->assertJsonPath('message', 'Reservation deleted');
        $response->assertJsonPath('status', 200);

        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id,
        ]);
    }

    public function test_destroy_returns_404_when_reservation_not_found(): void
    {
        $response = $this->deleteJson(route('v1.reservations.destroy', 999999));

        $response->assertStatus(404);
    }
}