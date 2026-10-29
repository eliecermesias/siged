<?php

namespace Database\Factories;

use App\Models\Characteristics;
use App\Models\InstrumentVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Characteristics>
 */
class CharacteristicsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'instrument_version_id' => InstrumentVersion::factory(),
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'position' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
