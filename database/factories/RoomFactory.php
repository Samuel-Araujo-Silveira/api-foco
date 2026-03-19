<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hotel;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hotel = Hotel::factory()->create();
        return [
            'id'              => $this->faker->unique()->randomNumber(9),
            'hotel_id'        => $hotel->id,
            'hotel_name'      => $hotel->name, 
            'name'            => $this->faker->randomElement(['Deluxe Double Room', 'Superior Chalet', 'Deluxe Triple Room']),
            'inventory_count' => $this->faker->numberBetween(1, 20),
        ];
    }
}
