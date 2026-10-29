<?php

namespace Database\Seeders;

use App\Models\Teachers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TeachersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isProduction() || ! Schema::hasTable('teachers')) {
            return;
        }

        $teachersToCreate = 20 - Teachers::query()->count();

        if ($teachersToCreate <= 0) {
            return;
        }

        for ($teacher = 0; $teacher < $teachersToCreate; $teacher++) {
            Teachers::factory()->create();
        }
    }
}
