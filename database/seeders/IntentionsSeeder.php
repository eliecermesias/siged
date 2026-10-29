<?php

namespace Database\Seeders;

use App\Models\Intention;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class IntentionsSeeder extends Seeder
{
    /**
     * @var array<int, string>
     */
    private const INTENTIONS = [
        'Autoevaluación institucional',
        'Evaluación docente',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('intentions')) {
            return;
        }

        foreach (self::INTENTIONS as $name) {
            Intention::query()->firstOrCreate(
                ['name' => $name],
                ['is_active' => true],
            );
        }
    }
}
