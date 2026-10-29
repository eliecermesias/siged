<?php

use App\Models\Coordinators;
use App\Models\DocumentTypes;
use App\Models\People;
use App\Models\Students;
use App\Models\Teachers;
use App\Models\User;
use Livewire\Livewire;

test('authenticated team members can view coordinators and open their forms', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'first_name' => 'Laura',
        'last_name' => 'Mendoza',
        'document' => '1234567890',
    ]);
    $coordinator = Coordinators::factory()->for($person, 'person')->create([
        'code' => 'COO-0001',
    ]);
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $indexResponse = $this
        ->actingAs($user)
        ->get(route('coordinators.index', $routeParameters));

    $indexResponse
        ->assertOk()
        ->assertSee($coordinator->code)
        ->assertSee('Laura Mendoza')
        ->assertSee('1234567890')
        ->assertSee('Activo');

    $createResponse = $this->get(route('coordinators.create', $routeParameters));

    $createResponse
        ->assertOk()
        ->assertSee('Buscar persona');

    $editResponse = $this->get(route('coordinators.edit', [...$routeParameters, 'coordinators' => $coordinator]));

    $editResponse
        ->assertOk()
        ->assertSee('Mendoza, Laura — 1234567890');
});

test('the coordinator form autocompletes available people and creates the selected coordinator', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'first_name' => 'Ana',
        'last_name' => 'Díaz',
        'document' => '0987654321',
    ]);
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    Livewire::test('coordinators.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', '098765')
        ->assertSee('Ana Díaz')
        ->assertSee('0987654321')
        ->call('selectPerson', $person->id)
        ->assertSet('personId', $person->id)
        ->call('createCoordinator')
        ->assertRedirect(route('coordinators.index', $routeParameters));

    $coordinator = Coordinators::query()->where('person_id', $person->id)->firstOrFail();

    expect($coordinator->code)->toStartWith('C');
    expect(mb_strlen($coordinator->code))->toBe(10);
    expect($coordinator->is_active)->toBeTrue();
});

test('a missing person can be created with a coordinator from the modal', function () {
    $user = User::factory()->create();
    $documentType = DocumentTypes::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    Livewire::test('coordinators.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', 'persona nueva')
        ->call('openCreatePersonModal')
        ->assertSet('showCreatePersonModal', true)
        ->set('firstName', 'Carolina')
        ->set('lastName', 'Rojas')
        ->set('documentTypeId', (string) $documentType->id)
        ->set('document', '2345678901')
        ->set('email', 'carolina.rojas@example.com')
        ->set('phone', '3001234567')
        ->call('createPersonAndCoordinator')
        ->assertRedirect(route('coordinators.index', $routeParameters));

    $person = People::query()->where('document', '2345678901')->firstOrFail();
    $coordinator = Coordinators::query()->where('person_id', $person->id)->firstOrFail();

    expect($person->first_name)->toBe('Carolina');
    expect($coordinator->code)->toStartWith('C');
    expect($coordinator->is_active)->toBeTrue();
    $this->assertDatabaseCount('people', 1);
    $this->assertDatabaseCount('coordinators', 1);
});

test('the coordinator modal leaves records unchanged when person details are invalid', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'document' => '3456789012',
        'email' => 'existing-person@example.com',
    ]);

    Livewire::test('coordinators.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', 'not found')
        ->call('openCreatePersonModal')
        ->set('firstName', 'Otra')
        ->set('lastName', 'Persona')
        ->set('documentTypeId', (string) $person->document_type_id)
        ->set('document', $person->document)
        ->set('email', $person->email)
        ->set('phone', '')
        ->call('createPersonAndCoordinator')
        ->assertHasErrors(['document', 'email']);

    $this->assertDatabaseCount('people', 1);
    $this->assertDatabaseCount('coordinators', 0);
});

test('coordinators can be searched and the search is retained across pages', function () {
    $user = User::factory()->create();

    foreach (range(1, 16) as $number) {
        $person = People::factory()->create([
            'email' => "coordinator-match-{$number}@example.com",
        ]);

        Coordinators::factory()->for($person, 'person')->create([
            'code' => sprintf('C%09d', $number),
        ]);
    }

    $unrelatedPerson = People::factory()->create([
        'email' => 'unrelated@example.com',
    ]);
    Coordinators::factory()->for($unrelatedPerson, 'person')->create([
        'code' => 'C999999999',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('coordinators.index', [
            'current_team' => $user->currentTeam->slug,
            'search' => 'coordinator-match',
            'page' => 2,
        ]));

    $response
        ->assertOk()
        ->assertSee('16 registros')
        ->assertSee('C000000016')
        ->assertDontSee('C000000001')
        ->assertDontSee('C999999999')
        ->assertSee('search=coordinator-match');
});

test('the coordinator factory assigns distinct people without removing their other roles', function () {
    $people = People::factory()->count(3)->create();
    $teacher = Teachers::factory()->for($people[0], 'person')->create();
    $student = Students::factory()->for($people[0], 'person')->create();

    $coordinators = collect();

    foreach (range(1, 3) as $number) {
        $coordinators->push(Coordinators::factory()->create());
    }

    expect(People::query()->count())->toBe(3);
    expect($coordinators->pluck('person_id')->unique())->toHaveCount(3);
    expect($coordinators->pluck('person_id')->contains($people[0]->id))->toBeTrue();
    $this->assertModelExists($teacher);
    $this->assertModelExists($student);
});

test('authenticated team members can create, update, and delete a coordinator', function () {
    $user = User::factory()->create();
    $person = People::factory()->create();
    $updatedPerson = People::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $createResponse = $this
        ->actingAs($user)
        ->post(route('coordinators.store', $routeParameters), [
            'person_id' => $person->id,
        ]);

    $createResponse
        ->assertRedirect(route('coordinators.index', $routeParameters))
        ->assertSessionHas('success', 'El coordinador fue creado correctamente.');

    $coordinator = Coordinators::query()->where('person_id', $person->id)->firstOrFail();

    expect($coordinator->code)->toStartWith('C');
    expect(mb_strlen($coordinator->code))->toBe(10);
    expect($coordinator->is_active)->toBeTrue();
    expect($coordinator->person->is($person))->toBeTrue();
    expect($person->coordinator->is($coordinator))->toBeTrue();

    $updateResponse = $this
        ->actingAs($user)
        ->put(route('coordinators.update', [...$routeParameters, 'coordinators' => $coordinator]), [
            'person_id' => $updatedPerson->id,
            'code' => 'COO-0003',
            'is_active' => false,
        ]);

    $updateResponse
        ->assertRedirect(route('coordinators.index', $routeParameters))
        ->assertSessionHas('success', 'La información del coordinador fue actualizada correctamente.');
    $this->assertDatabaseHas('coordinators', [
        'id' => $coordinator->id,
        'person_id' => $updatedPerson->id,
        'code' => 'COO-0003',
        'is_active' => false,
    ]);

    $coordinator->refresh();
    expect($coordinator->is_active)->toBeFalse();

    $deleteResponse = $this
        ->actingAs($user)
        ->delete(route('coordinators.destroy', [...$routeParameters, 'coordinators' => $coordinator]));

    $deleteResponse
        ->assertRedirect(route('coordinators.index', $routeParameters))
        ->assertSessionHas('success', 'El coordinador fue eliminado correctamente.');
    $this->assertModelMissing($coordinator);
});

test('a person can only be assigned to one coordinator', function () {
    $user = User::factory()->create();
    $person = People::factory()->create();
    Coordinators::factory()->for($person, 'person')->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $response = $this
        ->actingAs($user)
        ->from(route('coordinators.create', $routeParameters))
        ->post(route('coordinators.store', $routeParameters), [
            'person_id' => $person->id,
        ]);

    $response
        ->assertRedirect(route('coordinators.create', $routeParameters))
        ->assertInvalid(['person_id']);
    $this->assertDatabaseCount('coordinators', 1);
});

test('coordinators require authentication and team membership', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $routeParameters = ['current_team' => $owner->currentTeam->slug];

    $guestResponse = $this->get(route('coordinators.index', $routeParameters));

    $guestResponse->assertRedirect('/');

    $memberResponse = $this
        ->actingAs($otherUser)
        ->get(route('coordinators.index', $routeParameters));

    $memberResponse->assertForbidden();
});
