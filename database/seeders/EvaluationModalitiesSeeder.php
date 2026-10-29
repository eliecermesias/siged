<?php

namespace Database\Seeders;

use App\Models\EvaluationModality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EvaluationModalitiesSeeder extends Seeder
{
    /**
     * @var array<int, string>
     */
    private const MODALITIES = [
        'Autoevaluación',
        'Heteroevaluación',
        'Coevaluación',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('evaluation_modalities')) {
            return;
        }

        foreach (self::MODALITIES as $name) {
            EvaluationModality::query()->firstOrCreate(
                ['name' => $name],
                ['is_active' => true],
            );
        }
    }
}
