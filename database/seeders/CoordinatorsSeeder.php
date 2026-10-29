<?php

namespace Database\Seeders;

use App\Models\Coordinators;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CoordinatorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isProduction() || ! Schema::hasTable('coordinators')) {
            return;
        }

        $coordinatorsToCreate = 20 - Coordinators::query()->count();

        if ($coordinatorsToCreate <= 0) {
            return;
        }

        for ($coordinator = 0; $coordinator < $coordinatorsToCreate; $coordinator++) {
            Coordinators::factory()->create();
        }
    }
}
