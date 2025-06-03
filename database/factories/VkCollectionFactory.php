<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VkCollection>
 */
class VkCollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vk_collection_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'title' => $this->faker->sentence(3),
            'is_active' => $this->faker->boolean(90),
            'synced_at' => $this->faker->optional()->dateTimeThisYear(), // 50% chance заполнения
        ];
    }
}
