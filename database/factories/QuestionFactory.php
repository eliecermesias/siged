<?php

namespace Database\Factories;

use App\Models\Characteristics;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'characteristic_id' => Characteristics::factory(),
            'question_text' => fake()->sentence(),
            'response_type' => fake()->randomElement(['likert', 'yes_no', 'open_text']),
            'is_required' => true,
            'position' => fake()->numberBetween(1, 50),
            'is_active' => true,
        ];
    }
}
