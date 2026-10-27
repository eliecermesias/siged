<?php

use App\Models\DocumentTypes;
use App\Models\People;
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
