<?php

namespace Database\Factories;

use App\Models\ReservationRoom;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Room;
use App\Models\Reservation;

/**
 * @extends Factory<ReservationRoom>
 */
class ReservationRoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arrivalDate = $this->faker->dateTimeBetween('+1 days', '+30 days');
        $departureDate = (clone $arrivalDate)->modify('+' . rand(1, 7) . ' days');

        return [
            'id'             => $this->faker->unique()->randomNumber(9),
            'room_id'        => Room::factory(),
            'reservation_id' => Reservation::factory(),
            'arrival_date'   => $arrivalDate->format('Y-m-d'),
            'departure_date' => $departureDate->format('Y-m-d'),
            'currencycode'   => 'BRL',
            'meal_plan'      => $this->faker->randomElement(['Breakfast included.', 'No meals.', 'All inclusive.']),
            'totalprice'     => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
