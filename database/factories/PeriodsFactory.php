<?php

namespace Database\Factories;

use App\Models\Periods;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Periods>
 */
class PeriodsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2000, 2099);
        $period = fake()->randomElement(['A', 'B']);
        $startMonth = $period === 'A' ? 1 : 7;
        $endMonth = $period === 'A' ? 6 : 12;

        return [
            'year' => $year,
            'period' => $year.$period,
            'start_date' => sprintf('%04d-%02d-01', $year, $startMonth),
            'end_date' => sprintf('%04d-%02d-%02d', $year, $endMonth, $endMonth === 6 ? 30 : 31),
            'is_active' => true,
        ];
    }
}
