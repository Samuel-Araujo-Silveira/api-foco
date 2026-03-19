<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hotel;
use App\Models\Customer;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'          => $this->faker->unique()->randomNumber(9),
            'date'        => now()->toDateString(),
            'time'        => now()->toTimeString(),
            'hotel_id'    => Hotel::factory(),
            'customer_id' => Customer::factory(),
        ];
    }
}
