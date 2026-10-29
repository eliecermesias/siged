<?php

use App\Models\Characteristics;
use App\Models\EvaluationInstrument;
use App\Models\EvaluationModality;
use App\Models\InstrumentVersion;
use App\Models\Intention;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;

test('question bank resources render the shared list and form views', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $question = Question::factory()->create();
    $characteristic = $question->characteristics;
    $version = $characteristic->instrumentVersion;
    $instrument = $version->evaluationInstrument;
    $modality = $instrument->evaluationModality;
    $intention = $instrument->intention;

    $resources = [
        ['intentions', 'intention', $intention],
        ['evaluation-modalities', 'evaluationModality', $modality],
        ['evaluation-instruments', 'evaluationInstrument', $instrument],
        ['instrument-versions', 'instrumentVersion', $version],
        ['characteristics', 'characteristic', $characteristic],
        ['questions', 'question', $question],
    ];

    $this->actingAs($user);

    foreach ($resources as [$route, $parameter, $record]) {
        $indexResponse = $this->get(route($route.'.index', $routeParameters))
            ->assertOk()
            ->assertViewIs('question-bank.index')
            ->assertSee('Buscar')
            ->assertSee('Agregar registro')
            ->assertDontSee('Inicio')
            ->assertSee('Editar');

        $formResponse = $this->get(route($route.'.create', $routeParameters))
            ->assertOk()
            ->assertViewIs('question-bank.form')
            ->assertSee('Guardar');

        $this->get(route($route.'.edit', [...$routeParameters, $parameter => $record]))
            ->assertOk()
            ->assertViewIs('question-bank.form')
            ->assertSee('Guardar cambios');

        if ($route === 'questions') {
            $indexResponse->assertDontSee(route('intentions.index', $routeParameters), false);
            $formResponse->assertDontSee(route('intentions.index', $routeParameters), false);
            $indexResponse->assertDontSee('Manage your profile and account settings');
            $formResponse->assertDontSee('Manage your profile and account settings');
        } else {
            $indexResponse->assertSee(route('intentions.index', $routeParameters), false);
            $formResponse->assertSee(route('intentions.index', $routeParameters), false);
            $indexResponse->assertSee('Manage your profile and account settings');
            $formResponse->assertSee('Manage your profile and account settings');
        }
    }
});

test('question listings retain search criteria and paginate matching records', function () {
    $user = User::factory()->create();
    $matchingCharacteristic = Characteristics::factory()->create(['name' => 'Matching characteristic']);
    Question::factory()
        ->count(16)
        ->for($matchingCharacteristic, 'characteristics')
        ->sequence(fn (Sequence $sequence): array => [
            'question_text' => 'Matching question '.($sequence->index + 1),
            'position' => $sequence->index + 1,
        ])
        ->create();
    Question::factory()->create(['question_text' => 'Unrelated question']);

    $response = $this->actingAs($user)
        ->get(route('questions.index', [
            'current_team' => $user->currentTeam->slug,
            'search' => 'Matching',
            'page' => 2,
        ]));

    $response
        ->assertOk()
        ->assertViewHas('records', fn ($records): bool => $records->total() === 16)
        ->assertSee('Matching question 16')
        ->assertSee('search=Matching')
        ->assertSee('page=1');

    expect($response->viewData('records')->getCollection())->toHaveCount(1);
});

test('question creation rejects unsupported response types and unknown characteristics', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('questions.create', ['current_team' => $user->currentTeam->slug]))
        ->post(route('questions.store', ['current_team' => $user->currentTeam->slug]), [
            'characteristic_id' => 99999,
            'question_text' => 'Pregunta inválida',
            'response_type' => 'unsupported',
            'is_required' => true,
            'position' => 1,
            'is_active' => true,
        ])
        ->assertRedirect(route('questions.create', ['current_team' => $user->currentTeam->slug]))
        ->assertInvalid(['characteristic_id', 'response_type']);

    $this->assertDatabaseCount('questions', 0);
});

test('question bank prevents deleting records that still have dependent records', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $question = Question::factory()->create();
    $characteristic = $question->characteristics;
    $version = $characteristic->instrumentVersion;
    $instrument = $version->evaluationInstrument;

    $protectedRecords = [
        ['intentions', 'intention', $instrument->intention, 'No se puede eliminar una intención asociada a instrumentos.'],
        ['evaluation-modalities', 'evaluationModality', $instrument->evaluationModality, 'No se puede eliminar una modalidad asociada a instrumentos.'],
        ['evaluation-instruments', 'evaluationInstrument', $instrument, 'No se puede eliminar un instrumento que ya tiene versiones.'],
        ['instrument-versions', 'instrumentVersion', $version, 'No se puede eliminar una versión con características asociadas.'],
        ['characteristics', 'characteristic', $characteristic, 'No se puede eliminar una característica que tiene preguntas asociadas.'],
    ];

    $this->actingAs($user);

    foreach ($protectedRecords as [$route, $parameter, $record, $message]) {
        $this->delete(route($route.'.destroy', [...$routeParameters, $parameter => $record]))
            ->assertRedirect(route($route.'.index', $routeParameters))
            ->assertSessionHas('error', $message);

        $this->assertModelExists($record);
    }
});

test('question bank resources can be created updated and deleted through the shared form', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $intention = Intention::factory()->create();
    $modality = EvaluationModality::factory()->create();
    $this->actingAs($user);

    $this->post(route('intentions.store', $routeParameters), [
        'name' => 'Nueva intención',
        'description' => 'Propósito de prueba',
        'is_active' => true,
    ])->assertRedirect(route('intentions.index', $routeParameters))
        ->assertSessionHas('success', 'La intención fue creada correctamente.');
    $newIntention = Intention::query()->where('name', 'Nueva intención')->firstOrFail();
    $this->put(route('intentions.update', [...$routeParameters, 'intention' => $newIntention]), [
        'name' => 'Intención actualizada',
        'description' => 'Propósito actualizado',
        'is_active' => false,
    ])->assertRedirect(route('intentions.index', $routeParameters));
    $this->assertDatabaseHas('intentions', ['id' => $newIntention->id, 'name' => 'Intención actualizada', 'is_active' => false]);

    $this->post(route('evaluation-modalities.store', $routeParameters), [
        'name' => 'Nueva modalidad',
        'description' => 'Modalidad de prueba',
        'is_active' => true,
    ])->assertRedirect(route('evaluation-modalities.index', $routeParameters));
    $newModality = EvaluationModality::query()->where('name', 'Nueva modalidad')->firstOrFail();
    $this->put(route('evaluation-modalities.update', [...$routeParameters, 'evaluationModality' => $newModality]), [
        'name' => 'Modalidad actualizada',
        'description' => 'Modalidad actualizada',
        'is_active' => false,
    ])->assertRedirect(route('evaluation-modalities.index', $routeParameters));
    $this->assertDatabaseHas('evaluation_modalities', ['id' => $newModality->id, 'name' => 'Modalidad actualizada', 'is_active' => false]);

    $this->post(route('evaluation-instruments.store', $routeParameters), [
        'intention_id' => $intention->id,
        'evaluation_modality_id' => $modality->id,
        'name' => 'Nuevo instrumento',
        'description' => 'Instrumento de prueba',
        'is_active' => true,
    ])->assertRedirect(route('evaluation-instruments.index', $routeParameters));
    $instrument = EvaluationInstrument::query()->where('name', 'Nuevo instrumento')->firstOrFail();
    $this->put(route('evaluation-instruments.update', [...$routeParameters, 'evaluationInstrument' => $instrument]), [
        'intention_id' => $intention->id,
        'evaluation_modality_id' => $modality->id,
        'name' => 'Instrumento actualizado',
        'description' => 'Instrumento actualizado',
        'is_active' => false,
    ])->assertRedirect(route('evaluation-instruments.index', $routeParameters));

    $this->post(route('instrument-versions.store', $routeParameters), [
        'evaluation_instrument_id' => $instrument->id,
        'version_number' => 1,
        'notes' => 'Versión inicial',
        'is_active' => true,
    ])->assertRedirect(route('instrument-versions.index', $routeParameters));
    $version = InstrumentVersion::query()->where('evaluation_instrument_id', $instrument->id)->firstOrFail();
    $this->put(route('instrument-versions.update', [...$routeParameters, 'instrumentVersion' => $version]), [
        'evaluation_instrument_id' => $instrument->id,
        'version_number' => 2,
        'notes' => 'Versión actualizada',
        'is_active' => false,
    ])->assertRedirect(route('instrument-versions.index', $routeParameters));

    $this->post(route('characteristics.store', $routeParameters), [
        'instrument_version_id' => $version->id,
        'name' => 'Nueva característica',
        'description' => 'Característica de prueba',
        'position' => 1,
        'is_active' => true,
    ])->assertRedirect(route('characteristics.index', $routeParameters));
    $characteristic = Characteristics::query()->where('name', 'Nueva característica')->firstOrFail();
    $this->put(route('characteristics.update', [...$routeParameters, 'characteristic' => $characteristic]), [
        'instrument_version_id' => $version->id,
        'name' => 'Característica actualizada',
        'description' => 'Característica actualizada',
        'position' => 2,
        'is_active' => false,
    ])->assertRedirect(route('characteristics.index', $routeParameters));

    $this->post(route('questions.store', $routeParameters), [
        'characteristic_id' => $characteristic->id,
        'question_text' => 'Nueva pregunta',
        'response_type' => 'likert',
        'is_required' => true,
        'position' => 1,
        'is_active' => true,
    ])->assertRedirect(route('questions.index', $routeParameters));
    $question = Question::query()->where('question_text', 'Nueva pregunta')->firstOrFail();
    $this->put(route('questions.update', [...$routeParameters, 'question' => $question]), [
        'characteristic_id' => $characteristic->id,
        'question_text' => 'Pregunta actualizada',
        'response_type' => 'yes_no',
        'is_required' => false,
        'position' => 2,
        'is_active' => false,
    ])->assertRedirect(route('questions.index', $routeParameters));
    $this->assertDatabaseHas('questions', ['id' => $question->id, 'question_text' => 'Pregunta actualizada', 'response_type' => 'yes_no']);

    $this->delete(route('questions.destroy', [...$routeParameters, 'question' => $question]))->assertRedirect(route('questions.index', $routeParameters));
    $this->delete(route('characteristics.destroy', [...$routeParameters, 'characteristic' => $characteristic]))->assertRedirect(route('characteristics.index', $routeParameters));
    $this->delete(route('instrument-versions.destroy', [...$routeParameters, 'instrumentVersion' => $version]))->assertRedirect(route('instrument-versions.index', $routeParameters));
    $this->delete(route('evaluation-instruments.destroy', [...$routeParameters, 'evaluationInstrument' => $instrument]))->assertRedirect(route('evaluation-instruments.index', $routeParameters));
    $this->delete(route('evaluation-modalities.destroy', [...$routeParameters, 'evaluationModality' => $newModality]))->assertRedirect(route('evaluation-modalities.index', $routeParameters));
    $this->delete(route('intentions.destroy', [...$routeParameters, 'intention' => $newIntention]))->assertRedirect(route('intentions.index', $routeParameters));
});
