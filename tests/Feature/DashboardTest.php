<?php

use App\Models\User;
use Database\Seeders\MenuSeeder;

test('guests are redirected to the home page', function () {
    User::factory()->create();

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('home'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    app(MenuSeeder::class)->run();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
    $response->assertSee('Administración');
    $response->assertSee('Usuarios');
});
