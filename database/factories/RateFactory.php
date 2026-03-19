<?php

namespace Database\Factories;

use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hotel;

/**
 * @extends Factory<Rate>
 */
class RateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'       => $this->faker->unique()->randomNumber(8),
            'hotel_id' => Hotel::factory(),
            'name'     => $this->faker->randomElement(['Standard Rate', 'Breakfast included', 'All inclusive']),
            'active'   => 1,
            'price'    => $this->faker->randomFloat(2, 100, 500),
        ];
    }
}
