<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'discount_id' => $this->faker->optional(0.3)->randomElement(Discount::pluck('id')),
            'total_amount' => $this->faker->numberBetween(1000, 100000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'delivery_method' => $this->faker->randomElement(['courier', 'pickup']),
            'payment_method' => $this->faker->randomElement(['card', 'cash']),
        ];
    }
}
