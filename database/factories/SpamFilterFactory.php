<?php

namespace Database\Factories;

use App\Models\SpamFilter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpamFilter>
 */
class SpamFilterFactory extends Factory
{
    protected $model = SpamFilter::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['keyword', 'ip', 'user_id']),
            'value' => $this->faker->word,
            'is_active' => true,
        ];
    }
}
