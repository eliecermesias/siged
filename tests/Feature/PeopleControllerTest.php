<?php

use App\Models\Coordinators;
use App\Models\DocumentTypes;
use App\Models\People;
use App\Models\Students;
use App\Models\Teachers;
use App\Models\User;

test('authenticated team members can access the people index', function () {
    $user = User::factory()->create();
    $documentType = DocumentTypes::factory()->create(['name' => 'Cédula de ciudadanía']);
    $person = People::factory()
        ->for($documentType, 'documentType')
        ->create([
            'first_name' => 'Ana',
            'last_name' => 'Gómez',
            'email' => 'ana.gomez@example.com',
        ]);

    $response = $this
        ->actingAs($user)
        ->get(route('people.index', ['current_team' => $user->currentTeam->slug]));

    $response
        ->assertOk()
        ->assertSee('Ana Gómez')
        ->assertSee($documentType->name)
        ->assertSee($person->email);
});

test('the people index shows one badge for single, multiple, and missing roles', function () {
    $user = User::factory()->create();
    $multipleRoles = People::factory()->create(['first_name' => 'Multiple']);
    $teacherOnly = People::factory()->create(['first_name' => 'Teacher']);
    $studentOnly = People::factory()->create(['first_name' => 'Student']);
    $coordinatorOnly = People::factory()->create(['first_name' => 'Coordinator']);
    $unassigned = People::factory()->create(['first_name' => 'Unassigned']);

    Teachers::factory()->for($multipleRoles, 'person')->create();
    Students::factory()->for($multipleRoles, 'person')->create();
    Coordinators::factory()->for($multipleRoles, 'person')->create();
    Teachers::factory()->for($teacherOnly, 'person')->create();
    Students::factory()->for($studentOnly, 'person')->create();
    Coordinators::factory()->for($coordinatorOnly, 'person')->create();

    $multipleRoles->load(['teacher', 'student', 'coordinator']);
    $teacherOnly->load(['teacher', 'student', 'coordinator']);
    $studentOnly->load(['teacher', 'student', 'coordinator']);
    $coordinatorOnly->load(['teacher', 'student', 'coordinator']);
    $unassigned->load(['teacher', 'student', 'coordinator']);

    expect($multipleRoles->roleLabel())->toBe('Múltiples');
    expect($teacherOnly->roleLabel())->toBe('Docente');
    expect($studentOnly->roleLabel())->toBe('Estudiante');
    expect($coordinatorOnly->roleLabel())->toBe('Administrativo');
    expect($unassigned->roleLabel())->toBe('Sin rol');

    $response = $this
        ->actingAs($user)
        ->get(route('people.index', ['current_team' => $user->currentTeam->slug]));

    $response
        ->assertOk()
        ->assertSee('Múltiples')
        ->assertSee('Docente')
        ->assertSee('Estudiante')
        ->assertSee('Administrativo')
        ->assertSee('Sin rol');
});

test('people can be searched and the search is retained across pages', function () {
    $user = User::factory()->create();

    foreach (range(1, 16) as $number) {
        People::factory()->create([
            'first_name' => 'Lucía',
            'last_name' => 'Prueba',
            'email' => "search-match-{$number}@example.com",
        ]);
    }

    People::factory()->create([
        'first_name' => 'Otra',
        'last_name' => 'Persona',
        'email' => 'unrelated@example.com',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('people.index', [
            'current_team' => $user->currentTeam->slug,
            'search' => 'search-match',
            'page' => 2,
        ]));

    $response
        ->assertOk()
        ->assertSee('16 registros')
        ->assertSee('search-match-16@example.com')
        ->assertDontSee('search-match-1@example.com')
        ->assertDontSee('unrelated@example.com')
        ->assertSee('search=search-match');
});

test('the legacy people URL redirects to the current team route', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/personas');

    $response->assertRedirect(route('people.index', [
        'current_team' => $user->currentTeam->slug,
    ]));
});

test('authenticated team members can open the person creation form', function () {
    $user = User::factory()->create();
    $documentType = DocumentTypes::factory()->create(['name' => 'Cédula de ciudadanía']);

    $response = $this
        ->actingAs($user)
        ->get(route('people.create', ['current_team' => $user->currentTeam->slug]));

    $response
        ->assertOk()
        ->assertSee('Agregar persona')
        ->assertSee($documentType->name);
});

test('authenticated team members can create, update, and delete a person', function () {
    $user = User::factory()->create();
    $documentType = DocumentTypes::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $createResponse = $this
        ->actingAs($user)
        ->post(route('people.store', $routeParameters), [
            'document_type_id' => $documentType->id,
            'document' => '10203040',
            'first_name' => 'María',
            'last_name' => 'López',
            'email' => 'maria.lopez@example.com',
            'phone' => '3001234567',
            'address' => 'Calle 1 # 2-3',
            'birth_date' => '1995-05-20',
        ]);

    $createResponse
        ->assertRedirect(route('people.index', $routeParameters))
        ->assertSessionHas('success', 'La persona fue creada correctamente.');
    $this->assertDatabaseHas('people', [
        'document' => '10203040',
        'email' => 'maria.lopez@example.com',
    ]);

    $person = People::query()->where('document', '10203040')->firstOrFail();

    $updateResponse = $this
        ->actingAs($user)
        ->put(route('people.update', [...$routeParameters, 'people' => $person]), [
            'document_type_id' => $documentType->id,
            'document' => '10203040',
            'first_name' => 'María José',
            'last_name' => 'López',
            'email' => 'maria.lopez@example.com',
            'phone' => '3001234567',
            'address' => 'Carrera 4 # 5-6',
            'birth_date' => '1995-05-20',
        ]);

    $updateResponse
        ->assertRedirect(route('people.index', $routeParameters))
        ->assertSessionHas('success', 'La información de la persona fue actualizada correctamente.');
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'first_name' => 'María José',
        'address' => 'Carrera 4 # 5-6',
    ]);

    $deleteResponse = $this
        ->actingAs($user)
        ->delete(route('people.destroy', [...$routeParameters, 'people' => $person]));

    $deleteResponse
        ->assertRedirect(route('people.index', $routeParameters))
        ->assertSessionHas('success', 'La persona fue eliminada correctamente.');
    $this->assertModelMissing($person);
});

test('creating a person requires the mandatory data', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('people.create', ['current_team' => $user->currentTeam->slug]))
        ->post(route('people.store', ['current_team' => $user->currentTeam->slug]), []);

    $response
        ->assertRedirect(route('people.create', ['current_team' => $user->currentTeam->slug]))
        ->assertInvalid(['document_type_id', 'document', 'first_name', 'last_name', 'email']);
});
