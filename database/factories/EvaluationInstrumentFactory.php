<?php

namespace Database\Factories;

use App\Models\EvaluationInstrument;
use App\Models\EvaluationModality;
use App\Models\Intention;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EvaluationInstrument>
 */
class EvaluationInstrumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'intention_id' => Intention::factory(),
            'evaluation_modality_id' => EvaluationModality::factory(),
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
