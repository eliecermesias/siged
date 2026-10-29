<?php

use App\Models\DocumentTypes;
use App\Models\People;
use App\Models\Students;
use App\Models\User;
use Livewire\Livewire;

test('authenticated team members can view students and open their forms', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'first_name' => 'Laura',
        'last_name' => 'Mendoza',
        'document' => '1234567890',
    ]);
    $student = Students::factory()->for($person, 'person')->create([
        'code' => 'EST-0001',
    ]);
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $indexResponse = $this
        ->actingAs($user)
        ->get(route('students.index', $routeParameters));

    $indexResponse
        ->assertOk()
        ->assertSee($student->code)
        ->assertSee('Laura Mendoza')
        ->assertSee('1234567890')
        ->assertSee('Activo')
        ->assertSee(route('students.index', $routeParameters));

    $createResponse = $this->get(route('students.create', $routeParameters));

    $createResponse
        ->assertOk()
        ->assertSee('Buscar persona');

    $editResponse = $this->get(route('students.edit', [...$routeParameters, 'students' => $student]));

    $editResponse
        ->assertOk()
        ->assertSee('Mendoza, Laura — 1234567890');
});

test('the student form autocompletes available people and creates the selected student', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'first_name' => 'Ana',
        'last_name' => 'Díaz',
        'document' => '0987654321',
    ]);
    $assignedPerson = People::factory()->create([
        'first_name' => 'Luis',
        'last_name' => 'Toro',
        'document' => '1111111111',
    ]);
    Students::factory()->for($assignedPerson, 'person')->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    Livewire::test('students.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', '098765')
        ->assertSee('Ana Díaz')
        ->assertSee('0987654321')
        ->call('openCreatePersonModal')
        ->assertSet('showCreatePersonModal', false)
        ->set('personSearch', '111111')
        ->assertSee('Ya es estudiante')
        ->set('personSearch', '098765')
        ->call('selectPerson', $person->id)
        ->assertSet('personId', $person->id)
        ->call('createStudent')
        ->assertRedirect(route('students.index', $routeParameters));

    $student = Students::query()->where('person_id', $person->id)->firstOrFail();

    expect($student->code)->toStartWith('E');
    expect(mb_strlen($student->code))->toBe(10);
    expect($student->is_active)->toBeTrue();
});

test('a missing person can be created with a student from the modal', function () {
    $user = User::factory()->create();
    $documentType = DocumentTypes::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    Livewire::test('students.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', 'persona nueva')
        ->call('openCreatePersonModal')
        ->assertSet('showCreatePersonModal', true)
        ->set('firstName', 'Carolina')
        ->set('lastName', 'Rojas')
        ->set('documentTypeId', (string) $documentType->id)
        ->set('document', '2345678901')
        ->set('email', 'carolina.rojas@example.com')
        ->set('phone', '3001234567')
        ->call('createPersonAndStudent')
        ->assertRedirect(route('students.index', $routeParameters));

    $person = People::query()->where('document', '2345678901')->firstOrFail();
    $student = Students::query()->where('person_id', $person->id)->firstOrFail();

    expect($person->first_name)->toBe('Carolina');
    expect($student->code)->toStartWith('E');
    expect($student->is_active)->toBeTrue();
    $this->assertDatabaseCount('people', 1);
    $this->assertDatabaseCount('students', 1);
});

test('the modal leaves person and student tables unchanged when person details are invalid', function () {
    $user = User::factory()->create();
    $person = People::factory()->create([
        'document' => '3456789012',
        'email' => 'existing-person@example.com',
    ]);

    Livewire::test('students.create-form', ['currentTeam' => $user->currentTeam->slug])
        ->set('personSearch', 'not found')
        ->call('openCreatePersonModal')
        ->set('firstName', 'Otra')
        ->set('lastName', 'Persona')
        ->set('documentTypeId', (string) $person->document_type_id)
        ->set('document', $person->document)
        ->set('email', $person->email)
        ->set('phone', '')
        ->call('createPersonAndStudent')
        ->assertHasErrors(['document', 'email']);

    $this->assertDatabaseCount('people', 1);
    $this->assertDatabaseCount('students', 0);
});

test('students can be searched and the search is retained across pages', function () {
    $user = User::factory()->create();

    foreach (range(1, 16) as $number) {
        $person = People::factory()->create([
            'email' => "student-match-{$number}@example.com",
        ]);

        Students::factory()->for($person, 'person')->create([
            'code' => sprintf('E%09d', $number),
        ]);
    }

    $unrelatedPerson = People::factory()->create([
        'email' => 'unrelated@example.com',
    ]);
    Students::factory()->for($unrelatedPerson, 'person')->create([
        'code' => 'E999999999',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('students.index', [
            'current_team' => $user->currentTeam->slug,
            'search' => 'student-match',
            'page' => 2,
        ]));

    $response
        ->assertOk()
        ->assertSee('16 registros')
        ->assertSee('E000000016')
        ->assertDontSee('E000000001')
        ->assertDontSee('E999999999')
        ->assertSee('search=student-match');
});

test('the student factory assigns distinct existing people', function () {
    $people = People::factory()->count(3)->create();

    $students = collect();

    foreach (range(1, 3) as $number) {
        $students->push(Students::factory()->create());
    }

    expect(People::query()->count())->toBe(3);
    expect($students->pluck('person_id')->unique())->toHaveCount(3);

    foreach ($students as $student) {
        expect($people->contains('id', $student->person_id))->toBeTrue();
    }
});

test('authenticated team members can create, update, and delete a student', function () {
    $user = User::factory()->create();
    $person = People::factory()->create();
    $updatedPerson = People::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $createResponse = $this
        ->actingAs($user)
        ->post(route('students.store', $routeParameters), [
            'person_id' => $person->id,
        ]);

    $createResponse
        ->assertRedirect(route('students.index', $routeParameters))
        ->assertSessionHas('success', 'El estudiante fue creado correctamente.');

    $student = Students::query()->where('person_id', $person->id)->firstOrFail();

    expect($student->code)->toStartWith('E');
    expect(mb_strlen($student->code))->toBe(10);
    expect($student->is_active)->toBeTrue();
    expect($student->person->is($person))->toBeTrue();
    expect($person->student->is($student))->toBeTrue();

    $updateResponse = $this
        ->actingAs($user)
        ->put(route('students.update', [...$routeParameters, 'students' => $student]), [
            'person_id' => $updatedPerson->id,
            'code' => 'EST-0003',
            'is_active' => false,
        ]);

    $updateResponse
        ->assertRedirect(route('students.index', $routeParameters))
        ->assertSessionHas('success', 'La información del estudiante fue actualizada correctamente.');
    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'person_id' => $updatedPerson->id,
        'code' => 'EST-0003',
        'is_active' => false,
    ]);

    $student->refresh();
    expect($student->is_active)->toBeFalse();

    $deleteResponse = $this
        ->actingAs($user)
        ->delete(route('students.destroy', [...$routeParameters, 'students' => $student]));

    $deleteResponse
        ->assertRedirect(route('students.index', $routeParameters))
        ->assertSessionHas('success', 'El estudiante fue eliminado correctamente.');
    $this->assertModelMissing($student);
});

test('creating a student requires an existing person not already assigned', function () {
    $user = User::factory()->create();
    $person = People::factory()->create();
    Students::factory()->for($person, 'person')->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $response = $this
        ->actingAs($user)
        ->from(route('students.create', $routeParameters))
        ->post(route('students.store', $routeParameters), [
            'person_id' => $person->id,
        ]);

    $response
        ->assertRedirect(route('students.create', $routeParameters))
        ->assertInvalid(['person_id']);
    $this->assertDatabaseCount('students', 1);
});

test('creating a student rejects an unregistered person', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];

    $response = $this
        ->actingAs($user)
        ->from(route('students.create', $routeParameters))
        ->post(route('students.store', $routeParameters), [
            'person_id' => 999999,
        ]);

    $response
        ->assertRedirect(route('students.create', $routeParameters))
        ->assertInvalid(['person_id']);
    $this->assertDatabaseCount('students', 0);
});

test('students require authentication and team membership', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $routeParameters = ['current_team' => $owner->currentTeam->slug];

    $guestResponse = $this->get(route('students.index', $routeParameters));

    $guestResponse->assertRedirect('/');

    $memberResponse = $this
        ->actingAs($otherUser)
        ->get(route('students.index', $routeParameters));

    $memberResponse->assertForbidden();
});
