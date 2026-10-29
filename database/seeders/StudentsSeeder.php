<?php

namespace Database\Seeders;

use App\Models\Students;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isProduction() || ! Schema::hasTable('students')) {
            return;
        }

        $studentsToCreate = 20 - Students::query()->count();

        if ($studentsToCreate <= 0) {
            return;
        }

        for ($student = 0; $student < $studentsToCreate; $student++) {
            Students::factory()->create();
        }
    }
}
