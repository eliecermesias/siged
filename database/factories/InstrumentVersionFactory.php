<?php

namespace Database\Factories;

use App\Models\EvaluationInstrument;
use App\Models\InstrumentVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstrumentVersion>
 */
class InstrumentVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evaluation_instrument_id' => EvaluationInstrument::factory(),
            'version_number' => 1,
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
