<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction() || User::query()->where('email', 'eliecer.mesias@gmail.com')->exists()) {
            return;
        }

        User::factory()->create([
            'name' => 'Eliecer Mesias',
            'email' => 'eliecer.mesias@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
    }
}
