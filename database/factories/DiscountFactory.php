<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->bothify('DISCOUNT-####')),
            'type' => $this->faker->randomElement(['percentage', 'fixed']),
            'amount' => $this->faker->numberBetween(10, 50),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'max_uses' => $this->faker->optional()->numberBetween(10, 100),
            'is_active' => true,
        ];
    }
}
