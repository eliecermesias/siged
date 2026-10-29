<?php

namespace Database\Seeders;

use App\Models\EvaluationInstrument;
use App\Models\EvaluationModality;
use App\Models\Intention;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EvaluationInstrumentsSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, intention: string, modality: string}>
     */
    private const INSTRUMENTS = [
        [
            'name' => 'Autoevaluación institucional',
            'intention' => 'Autoevaluación institucional',
            'modality' => 'Autoevaluación',
        ],
        [
            'name' => 'Evaluación docente - Autoevaluación',
            'intention' => 'Evaluación docente',
            'modality' => 'Autoevaluación',
        ],
        [
            'name' => 'Evaluación docente - Heteroevaluación',
            'intention' => 'Evaluación docente',
            'modality' => 'Heteroevaluación',
        ],
        [
            'name' => 'Evaluación docente - Coevaluación',
            'intention' => 'Evaluación docente',
            'modality' => 'Coevaluación',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('evaluation_instruments')
            || ! Schema::hasTable('intentions')
            || ! Schema::hasTable('evaluation_modalities')) {
            return;
        }

        foreach (self::INSTRUMENTS as $instrument) {
            $intention = Intention::query()
                ->where('name', $instrument['intention'])
                ->firstOrFail();
            $modality = EvaluationModality::query()
                ->where('name', $instrument['modality'])
                ->firstOrFail();

            EvaluationInstrument::query()->firstOrCreate(
                ['name' => $instrument['name']],
                [
                    'intention_id' => $intention->id,
                    'evaluation_modality_id' => $modality->id,
                    'is_active' => true,
                ],
            );
        }
    }
}
