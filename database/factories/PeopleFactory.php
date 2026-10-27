<?php

namespace Database\Factories;

use App\Models\DocumentTypes;
use App\Models\People;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<People>
 */
class PeopleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_type_id' => DocumentTypes::query()->inRandomOrder()->value('id') ?? DocumentTypes::factory(),
            'document' => fake()->unique()->numerify('##########'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->numerify('3#########'),
            'address' => fake()->optional()->address(),
            'birth_date' => fake()->optional()->date(),
        ];
    }
}
