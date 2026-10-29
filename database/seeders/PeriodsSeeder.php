<?php

namespace Database\Seeders;

use App\Models\Periods;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PeriodsSeeder extends Seeder
{
    /**
     * @var array<int, array{year: int, period: string}>
     */
    private const PERIODS = [
        ['year' => 2026, 'period' => '2026A'],
        ['year' => 2026, 'period' => '2026B'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('periods')) {
            return;
        }

        foreach (self::PERIODS as $period) {
            Periods::query()->firstOrCreate(
                ['period' => $period['period']],
                [
                    'year' => $period['year'],
                    'is_active' => true,
                ],
            );
        }
    }
}
