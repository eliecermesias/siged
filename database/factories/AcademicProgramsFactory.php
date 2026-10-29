<?php

namespace Database\Factories;

use App\Models\AcademicPrograms;
use App\Models\EducationLevel;
use App\Models\KnowledgeArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicPrograms>
 */
class AcademicProgramsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'education_level_id' => EducationLevel::factory(),
            'knowledge_area_id' => KnowledgeArea::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'snies' => fake()->unique()->numerify('######'),
            'resolution' => fake()->optional()->bothify('####-####'),
            'is_active' => true,
        ];
    }
}
