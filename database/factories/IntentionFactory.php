<?php

namespace Database\Factories;

use App\Models\Intention;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Intention>
 */
class IntentionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'is_active' => true,
        ];
    }
}
