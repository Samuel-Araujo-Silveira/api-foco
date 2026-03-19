<?php

namespace Database\Factories;

use App\Models\GuestCount;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ReservationRoom;

/**
 * @extends Factory<GuestCount>
 */
class GuestCountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'count' => $this->faker->numberBetween(1, 4),
            
            'type'  => $this->faker->randomElement(['adult', 'child', 'infant']),
        
            'reservation_room_id' => ReservationRoom::factory(),
        ];
    }
}
