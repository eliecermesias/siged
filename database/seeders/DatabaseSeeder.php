<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            MenuSeeder::class,
            DocumentTypesSeeder::class,
            PeopleSeeder::class,
            TeachersSeeder::class,
            StudentsSeeder::class,
            CoordinatorsSeeder::class,
            EducationLevelSeeder::class,
            KnowledgeAreaSeeder::class,
            AcademicProgramsSeeder::class,
            PeriodsSeeder::class,
            IntentionsSeeder::class,
            EvaluationModalitiesSeeder::class,
            EvaluationInstrumentsSeeder::class,
            InstrumentVersionsSeeder::class,
        ]);
    }
}
