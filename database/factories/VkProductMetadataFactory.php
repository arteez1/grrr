<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VkProductMetadata>
 */
class VkProductMetadataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'width' => $this->faker->numberBetween(100, 500),
            'height' => $this->faker->numberBetween(100, 500),
            'depth' => $this->faker->numberBetween(100, 500),
            'weight' => $this->faker->numberBetween(100, 2000),
            'vk_collection_ids' => [1, 5, 9], // Пример ID подборок
        ];
    }
}
