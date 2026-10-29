<?php

namespace Database\Factories;

use App\Models\People;
use App\Models\Teachers;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

/**
 * @extends Factory<Teachers>
 */
class TeachersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $personId = People::query()
            ->whereDoesntHave('teacher')
            ->inRandomOrder()
            ->value('id');

        if ($personId === null) {
            throw new RuntimeException('Cannot create a teacher without an existing person not already assigned to a teacher.');
        }

        return [
            'person_id' => $personId,
            'code' => fake()->unique()->numerify('T#########'),
            'is_active' => true,
        ];
    }
}
