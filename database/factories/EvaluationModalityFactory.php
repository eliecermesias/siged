<?php

namespace Database\Factories;

use App\Models\EvaluationModality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EvaluationModality>
 */
class EvaluationModalityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
