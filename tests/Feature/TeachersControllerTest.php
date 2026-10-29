<?php

use App\Models\DocumentTypes;
use App\Models\People;
use App\Models\Teachers;
use App\Models\User;
use Livewire\Livewire;

test('authenticated team members can view teachers and select a person', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'first_name' => 'Laura',
        'last_name' => 'Mendoza',
        'document' => '1234567890',
    ]);
    $availablePerson = People::factory()->create([
        'first_name' => 'Ana',
        'last_name' => 'Díaz',
        'document' => '0987654321',
    ]);
    $teacher = Teachers::factory()->for($person, 'person')->create([
        'code' => 'DOC-0001',
    ]);
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $indexResponse = $this
        ->actingAs($user)
        ->get(route('teachers.index', $routeParameters));

    $indexResponse
        ->assertOk()
        ->assertSee($teacher->code)
        ->assertSee('Laura Mendoza')
        ->assertSee('1234567890')
        ->assertSee('Activo')
        ->assertSee(route('teachers.index', $routeParameters));

    $createResponse = $this->get(route('teachers.create', $routeParameters));

    $createResponse
        ->assertOk()
        ->assertSee('Buscar persona');

    $editResponse = $this->get(route('teachers.edit', [...$routeParameters, 'teachers' => $teacher]));

    $editResponse
        ->assertOk()
        ->assertSee('Mendoza, Laura — 1234567890');
});

test('the teacher form autocompletes available people and creates the selected teacher', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'first_name' => 'Ana',
        'last_name' => 'Díaz',
        'document' => '0987654321',
    ]);
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    Livewire::test('teachers.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', '098765')
        ->assertSee('Ana Díaz')
        ->assertSee('0987654321')
        ->call('selectPerson', $person->id)
        ->assertSet('personId', $person->id)
        ->call('createTeacher')
        ->assertRedirect(route('teachers.index', $routeParameters));

    $teacher = Teachers::query()->where('person_id', $person->id)->firstOrFail();

    expect($teacher->code)->toStartWith('T');
    expect(strlen($teacher->code))->toBe(10);
    expect($teacher->is_active)->toBeTrue();
});

test('a missing person can be created with a teacher from the modal', function () {
    $user = User::factory()->create();
    $documentType = DocumentTypes::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    Livewire::test('teachers.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', 'persona nueva')
        ->call('openCreatePersonModal')
        ->assertSet('showCreatePersonModal', true)
        ->set('firstName', 'Carolina')
        ->set('lastName', 'Rojas')
        ->set('documentTypeId', (string) $documentType->id)
        ->set('document', '2345678901')
        ->set('email', 'carolina.rojas@example.com')
        ->set('phone', '3001234567')
        ->call('createPersonAndTeacher')
        ->assertRedirect(route('teachers.index', $routeParameters));

    $person = People::query()->where('document', '2345678901')->firstOrFail();
    $teacher = Teachers::query()->where('person_id', $person->id)->firstOrFail();

    expect($person->first_name)->toBe('Carolina');
    expect($teacher->code)->toStartWith('T');
    expect($teacher->is_active)->toBeTrue();
    $this->assertDatabaseCount('people', 1);
    $this->assertDatabaseCount('teachers', 1);
});

test('the modal leaves person and teacher tables unchanged when person details are invalid', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'document' => '3456789012',
        'email' => 'existing-person@example.com',
    ]);

    Livewire::test('teachers.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', 'not found')
        ->call('openCreatePersonModal')
        ->set('firstName', 'Otra')
        ->set('lastName', 'Persona')
        ->set('documentTypeId', (string) $person->document_type_id)
        ->set('document', $person->document)
        ->set('email', $person->email)
        ->set('phone', '')
        ->call('createPersonAndTeacher')
        ->assertHasErrors(['document', 'email']);

    $this->assertDatabaseCount('people', 1);
    $this->assertDatabaseCount('teachers', 0);
});

test('teachers can be searched and the search is retained across pages', function () {
    $user = User::factory()->create();

    foreach (range(1, 16) as $number) {
        $person = People::factory()->create([
            'email' => "teacher-match-{$number}@example.com",
        ]);

        Teachers::factory()->for($person, 'person')->create([
            'code' => sprintf('T%09d', $number),
        ]);
    }

    $unrelatedPerson = People::factory()->create([
        'email' => 'unrelated@example.com',
    ]);
    Teachers::factory()->for($unrelatedPerson, 'person')->create([
        'code' => 'T999999999',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('teachers.index', [
            'current_team' => $user->currentTeam->slug,
            'search' => 'teacher-match',
            'page' => 2,
        ]));

    $response
        ->assertOk()
        ->assertSee('16 registros')
        ->assertSee('T000000016')
        ->assertDontSee('T000000001')
        ->assertDontSee('T999999999')
        ->assertSee('search=teacher-match');
});

test('the teacher factory assigns distinct existing people', function () {
    $people = People::factory()->count(3)->create();

    $teachers = collect();

    foreach (range(1, 3) as $number) {
        $teachers->push(Teachers::factory()->create());
    }

    expect(People::query()->count())->toBe(3);
    expect($teachers->pluck('person_id')->unique())->toHaveCount(3);

    foreach ($teachers as $teacher) {
        expect($people->contains('id', $teacher->person_id))->toBeTrue();
    }
});

test('authenticated team members can create, update, and delete a teacher', function () {
    $user = User::factory()->create();
    $person = People::factory()->create();
    $updatedPerson = People::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $createResponse = $this
        ->actingAs($user)
        ->post(route('teachers.store', $routeParameters), [
            'person_id' => $person->id,
        ]);

    $createResponse
        ->assertRedirect(route('teachers.index', $routeParameters))
        ->assertSessionHas('success', 'El docente fue creado correctamente.');

    $teacher = Teachers::query()->where('person_id', $person->id)->firstOrFail();

    expect($teacher->code)->toStartWith('T');
    expect(strlen($teacher->code))->toBe(10);
    expect($teacher->is_active)->toBeTrue();
    expect($teacher->person->is($person))->toBeTrue();
    expect($person->teacher->is($teacher))->toBeTrue();

    $updateResponse = $this
        ->actingAs($user)
        ->put(route('teachers.update', [...$routeParameters, 'teachers' => $teacher]), [
            'person_id' => $updatedPerson->id,
            'code' => 'DOC-0003',
            'is_active' => false,
        ]);

    $updateResponse
        ->assertRedirect(route('teachers.index', $routeParameters))
        ->assertSessionHas('success', 'La información del docente fue actualizada correctamente.');
    $this->assertDatabaseHas('teachers', [
        'id' => $teacher->id,
        'person_id' => $updatedPerson->id,
        'code' => 'DOC-0003',
        'is_active' => false,
    ]);

    $teacher->refresh();
    expect($teacher->is_active)->toBeFalse();

    $deleteResponse = $this
        ->actingAs($user)
        ->delete(route('teachers.destroy', [...$routeParameters, 'teachers' => $teacher]));

    $deleteResponse
        ->assertRedirect(route('teachers.index', $routeParameters))
        ->assertSessionHas('success', 'El docente fue eliminado correctamente.');
    $this->assertModelMissing($teacher);
});

test('creating a teacher requires a registered person and a valid code', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $response = $this
        ->actingAs($user)
        ->from(route('teachers.create', $routeParameters))
        ->post(route('teachers.store', $routeParameters), [
            'person_id' => 999999,
        ]);

    $response
        ->assertRedirect(route('teachers.create', $routeParameters))
        ->assertInvalid(['person_id']);
});

test('a person can only be assigned to one teacher', function () {
    $user = User::factory()->create();
    $person = People::factory()->create();
    Teachers::factory()->for($person, 'person')->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $response = $this
        ->actingAs($user)
        ->from(route('teachers.create', $routeParameters))
        ->post(route('teachers.store', $routeParameters), [
            'person_id' => $person->id,
            'code' => 'DOC-0004',
            'is_active' => true,
        ]);

    $response
        ->assertRedirect(route('teachers.create', $routeParameters))
        ->assertInvalid(['person_id']);
    $this->assertDatabaseCount('teachers', 1);
});
