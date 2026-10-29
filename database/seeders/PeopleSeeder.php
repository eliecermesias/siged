<?php

namespace Database\Seeders;

use App\Models\People;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PeopleSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction() || ! Schema::hasTable('people') || ! Schema::hasTable('document_types')) {
            return;
        }

        $peopleToCreate = 60 - People::query()->count();

        if ($peopleToCreate <= 0) {
            return;
        }

        People::factory()->count($peopleToCreate)->create();
    }
}
