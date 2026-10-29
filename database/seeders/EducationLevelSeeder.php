<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EducationLevelSeeder extends Seeder
{
    /**
     * @var array<int, string>
     */
    private const LEVELS = [
        'Técnica profesional',
        'Tecnológica',
        'Universitario',
        'Especialización',
        'Maestría',
        'Doctorado',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('education_levels')) {
            return;
        }

        foreach (self::LEVELS as $name) {
            EducationLevel::query()->firstOrCreate(
                ['name' => $name],
                ['is_active' => true],
            );
        }
    }
}
