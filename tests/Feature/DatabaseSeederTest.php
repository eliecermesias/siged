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
    $this->assertDatabaseCount('students', 20);
    $this->assertDatabaseCount('coordinators', 20);
    $this->assertDatabaseCount('education_levels', 6);
    $this->assertDatabaseCount('knowledge_areas', 8);
    $this->assertDatabaseCount('academic_programs', 15);
    $this->assertDatabaseCount('periods', 2);
    $this->assertDatabaseCount('intentions', 2);
    $this->assertDatabaseCount('evaluation_modalities', 3);
    $this->assertDatabaseCount('evaluation_instruments', 4);
    $this->assertDatabaseCount('instrument_versions', 4);
});

test('the menu seed is idempotent and stores the configured hierarchy', function () {
    app(MenuSeeder::class)->run();
    app(MenuSeeder::class)->run();

    $this->assertDatabaseCount('menus', 23);
    $this->assertDatabaseHas('menus', [
        'name' => 'Administración',
        'slug' => 'administracion',
        'menu_id' => null,
    ]);
    $this->assertDatabaseMissing('menus', [
        'name' => 'Usuarios',
        'slug' => 'usuarios',
    ]);
    $this->assertDatabaseHas('menus', [
        'name' => 'Personas',
        'slug' => 'personas',
        'current' => 'people.*',
    ]);
    $this->assertDatabaseHas('menus', [
        'name' => 'Estudiantes',
        'slug' => 'estudiantes',
        'current' => 'students.*',
    ]);
    $this->assertDatabaseHas('menus', [
        'name' => 'Coordinadores',
        'slug' => 'coordinadores',
        'current' => 'coordinators.*',
    ]);
    $this->assertDatabaseHas('menus', [
        'name' => 'Programas académicos',
        'slug' => 'programas-academicos',
        'current' => 'academic-programs.*',
    ]);
    $this->assertDatabaseHas('menus', [
        'name' => 'Periodos',
        'slug' => 'periodos',
        'current' => 'periods.*',
    ]);
    $this->assertDatabaseHas('menus', [
        'name' => 'Cursos',
        'slug' => 'cursos',
        'url' => '#',
    ]);
    $this->assertDatabaseHas('menus', [
        'name' => 'Preguntas',
        'slug' => 'preguntas',
        'current' => 'questions.*',
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
