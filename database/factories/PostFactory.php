<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraphs(5, true),
            'short_content' => $this->faker->sentence,
            'main_image' => 'posts/main/' . $this->faker->image(storage_path('app/public/posts/main'), 800, 600, null, false),
            'vk_image' => $this->faker->optional()->imageUrl(800, 600),
            'tm_image' => $this->faker->optional()->imageUrl(1024, 768),
            'type' => $this->faker->randomElement(['article', 'news']),
            'user_id' => User::factory(),
        ];
    }
}
