<?php

namespace Database\Factories;

use App\Models\Coordinators;
use App\Models\People;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

/**
 * @extends Factory<Coordinators>
 */
class CoordinatorsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $personId = People::query()
            ->whereDoesntHave('coordinator')
            ->inRandomOrder()
            ->value('id');

        if ($personId === null) {
            throw new RuntimeException('Cannot create a coordinator without an existing person not already assigned to a coordinator.');
        }

        return [
            'person_id' => $personId,
            'code' => fake()->unique()->numerify('C#########'),
            'is_active' => true,
        ];
    }
}
