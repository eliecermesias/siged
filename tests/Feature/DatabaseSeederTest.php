<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\MenuSeeder;

test('the migration creates the local seed user and personal team', function () {
    $user = User::query()
        ->where('email', 'eliecer.mesias@gmail.com')
        ->firstOrFail();

    $this->assertModelExists($user);
    $this->assertDatabaseHas('teams', [
        'id' => $user->current_team_id,
        'is_personal' => true,
    ]);
    $this->assertDatabaseHas('team_members', [
        'team_id' => $user->current_team_id,
        'user_id' => $user->id,
        'role' => 'owner',
    ]);
});

test('running the seed again does not create duplicate users or teams', function () {
    app(DatabaseSeeder::class)->run();

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('teams', 1);
});

test('the menu seed is idempotent and stores the configured hierarchy', function () {
    app(MenuSeeder::class)->run();
    app(MenuSeeder::class)->run();

    $this->assertDatabaseCount('menus', 16);
    $this->assertDatabaseHas('menus', [
        'name' => 'Administración',
        'slug' => 'administracion',
        'menu_id' => null,
    ]);
    $this->assertDatabaseHas('menus', [
        'name' => 'Usuarios',
        'slug' => 'usuarios',
    ]);
});

test('the seed does not create a default account in production', function () {
    User::query()->where('email', 'eliecer.mesias@gmail.com')->delete();
    app()->detectEnvironment(fn (): string => 'production');

    app(DatabaseSeeder::class)->run();

    $this->assertDatabaseMissing('users', [
        'email' => 'eliecer.mesias@gmail.com',
    ]);
});
