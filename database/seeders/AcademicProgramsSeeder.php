<?php

namespace Database\Seeders;

use App\Models\AcademicPrograms;
use App\Models\EducationLevel;
use App\Models\KnowledgeArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AcademicProgramsSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, snies: string, education_level: string, knowledge_area: string}>
     */
    private const PROGRAMS = [
        ['name' => 'Administración de Empresas', 'snies' => '53257', 'education_level' => 'Universitario', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Administración de la Cadena de Suministro', 'snies' => '107203', 'education_level' => 'Universitario', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Comercio y Finanzas Internacionales', 'snies' => '105131', 'education_level' => 'Universitario', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Contaduría Pública', 'snies' => '53611', 'education_level' => 'Universitario', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Derecho', 'snies' => '111580', 'education_level' => 'Universitario', 'knowledge_area' => 'Ciencias Sociales y Humanas'],
        ['name' => 'Especialización en Gerencia de Proyectos', 'snies' => '105132', 'education_level' => 'Especialización', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Especialización en Seguridad y Salud en el Trabajo', 'snies' => '111655', 'education_level' => 'Especialización', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Ingeniería de Sistemas', 'snies' => '53735', 'education_level' => 'Universitario', 'knowledge_area' => 'Ingeniería/Arquitectura'],
        ['name' => 'Ingeniería Industrial', 'snies' => '90614', 'education_level' => 'Universitario', 'knowledge_area' => 'Ingeniería/Arquitectura'],
        ['name' => 'Profesional en Mercadeo Empresarial', 'snies' => '53610', 'education_level' => 'Universitario', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Tecnología en Desarrollo de Software', 'snies' => '104257', 'education_level' => 'Tecnológica', 'knowledge_area' => 'Ingeniería/Arquitectura'],
        ['name' => 'Tecnología en Gestión Contable y Financiera', 'snies' => '19543', 'education_level' => 'Tecnológica', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Tecnología en Gestión de la Producción', 'snies' => '2189', 'education_level' => 'Tecnológica', 'knowledge_area' => 'Ingeniería/Arquitectura'],
        ['name' => 'Tecnología en Gestión Empresarial', 'snies' => '104166', 'education_level' => 'Tecnológica', 'knowledge_area' => 'Economía/Administración/Contaduría'],
        ['name' => 'Tecnología en Mercadeo y Gestión Comercial', 'snies' => '104705', 'education_level' => 'Tecnológica', 'knowledge_area' => 'Economía/Administración/Contaduría'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('academic_programs')
            || ! Schema::hasTable('education_levels')
            || ! Schema::hasTable('knowledge_areas')) {
            return;
        }

        foreach (self::PROGRAMS as $program) {
            $educationLevel = EducationLevel::query()
                ->where('name', $program['education_level'])
                ->firstOrFail();
            $knowledgeArea = KnowledgeArea::query()
                ->where('name', $program['knowledge_area'])
                ->firstOrFail();

            AcademicPrograms::query()->firstOrCreate(
                ['snies' => $program['snies']],
                [
                    'education_level_id' => $educationLevel->id,
                    'knowledge_area_id' => $knowledgeArea->id,
                    'name' => $program['name'],
                    'is_active' => true,
                ],
            );
        }
    }
}
