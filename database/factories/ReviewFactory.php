<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['product', 'post', 'general']),
            'reviewable_id' => function (array $attributes) {
                return match ($attributes['type']) {
                    'product' => Product::factory(),
                    'post' => Post::factory(),
                    'general' => 0,
                };
            },
            'reviewable_type' => function (array $attributes) {
                return match ($attributes['type']) {
                    'product' => Product::class,
                    'post' => Post::class,
                    'general' => null,
                };
            },
            'client_id' => Client::factory(),
            'content' => $this->faker->paragraph,
            'rating' => $this->faker->numberBetween(1, 5),
            'is_approved' => $this->faker->boolean(70),
        ];
    }
}
