<?php

use App\Models\User;

test('question bank settings provide links to configuration modules but not question management', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $this->actingAs($user);

    $response = $this->get(route('settings.question-bank', $routeParameters));

    $response
        ->assertOk()
        ->assertSee('Configuración del banco')
        ->assertSee('Intenciones')
        ->assertSee('Modalidades')
        ->assertSee('Instrumentos')
        ->assertSee('Versiones')
        ->assertSee('Características')
        ->assertSee('Resumen')
        ->assertDontSee('Preguntas');

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee(route('settings.question-bank', $routeParameters), false)
        ->assertSee(route('intentions.index', $routeParameters), false)
        ->assertSee(route('evaluation-modalities.index', $routeParameters), false)
        ->assertSee(route('evaluation-instruments.index', $routeParameters), false)
        ->assertSee(route('instrument-versions.index', $routeParameters), false)
        ->assertSee(route('characteristics.index', $routeParameters), false);
});

test('question bank settings require membership in the selected team', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)
        ->get(route('settings.question-bank', [
            'current_team' => $otherUser->currentTeam->slug,
        ]))
        ->assertForbidden();
});
