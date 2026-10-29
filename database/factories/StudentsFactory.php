<?php

namespace Database\Factories;

use App\Models\People;
use App\Models\Students;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

/**
 * @extends Factory<Students>
 */
class StudentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $personId = People::query()
            ->whereDoesntHave('student')
            ->inRandomOrder()
            ->value('id');

        if ($personId === null) {
            throw new RuntimeException('Cannot create a student without an existing person not already assigned to a student.');
        }

        return [
            'person_id' => $personId,
            'code' => fake()->unique()->numerify('E#########'),
            'is_active' => true,
        ];
    }
}
