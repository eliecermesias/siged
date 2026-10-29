<?php

namespace Database\Seeders;

use App\Models\EvaluationInstrument;
use App\Models\InstrumentVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class InstrumentVersionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('instrument_versions') || ! Schema::hasTable('evaluation_instruments')) {
            return;
        }

        EvaluationInstrument::query()
            ->whereIn('name', [
                'Autoevaluación institucional',
                'Evaluación docente - Autoevaluación',
                'Evaluación docente - Heteroevaluación',
                'Evaluación docente - Coevaluación',
            ])
            ->each(function (EvaluationInstrument $instrument): void {
                InstrumentVersion::query()->firstOrCreate(
                    [
                        'evaluation_instrument_id' => $instrument->id,
                        'version_number' => 1,
                    ],
                    ['is_active' => true],
                );
            });
    }
}
