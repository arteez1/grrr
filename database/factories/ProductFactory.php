<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'sku' => $this->faker->unique()->ean13(),
            'description' => $this->faker->paragraph(3, true),
            'price' => $this->faker->randomFloat(2, 100, 10000),
            'old_price' => fake()->boolean(30) ? fake()->randomFloat(2, 150, 15000) : null,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'is_published' => fake()->boolean(80),
            'main_image' => [
                'main_image' => 'products/main/' . $this->faker->image(storage_path('app/public/products/main'), 800, 600, null, false),
            ],
            'vk_image' => $this->faker->optional()->imageUrl(800, 600),
            'tm_image' => $this->faker->optional()->imageUrl(1024, 768),
        ];
    }
}
