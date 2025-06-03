<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['product', 'post', 'news']);

        return [
            'name' => $this->faker->unique()->word(),
            'slug' => $this->faker->unique()->slug(),
            'type' => $type,
            'parent_id' => $type === 'product'
                ? Category::factory()->product()
                : null,
        ];
    }

    public function product(): CategoryFactory
    {
        return $this->state(['type' => 'product']);
    }

    public function post(): CategoryFactory
    {
        return $this->state(['type' => 'post']);
    }

    public function news(): CategoryFactory
    {
        return $this->state(['type' => 'news']);
    }
}
