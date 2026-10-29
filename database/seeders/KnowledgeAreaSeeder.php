<?php

namespace Database\Seeders;

use App\Models\KnowledgeArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class KnowledgeAreaSeeder extends Seeder
{
    /**
     * @var array<int, string>
     */
    private const AREAS = [
        'Agronomía',
        'Bellas Artes',
        'Ciencias de la Educación',
        'Ciencias de la Salud',
        'Ciencias Sociales y Humanas',
        'Economía/Administración/Contaduría',
        'Ingeniería/Arquitectura',
        'Matemáticas/Ciencias Naturales',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('knowledge_areas')) {
            return;
        }

        foreach (self::AREAS as $name) {
            KnowledgeArea::query()->firstOrCreate(
                ['name' => $name],
                ['is_active' => true],
            );
        }
    }
}
