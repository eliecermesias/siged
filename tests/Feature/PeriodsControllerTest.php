<?php

use App\Models\Periods;
use App\Models\User;
use Database\Seeders\PeriodsSeeder;

test('authenticated team members can view, create, update, and delete periods', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $this->actingAs($user);

    $this->get(route('periods.create', $routeParameters))
        ->assertOk()
        ->assertSee('Fecha de inicio')
        ->assertSee('Fecha final');

    $this->post(route('periods.store', $routeParameters), [
        'year' => 2026,
        'period' => '2026A',
        'start_date' => '2026-01-15',
        'end_date' => '2026-06-30',
        'is_active' => true,
    ])->assertRedirect(route('periods.index', $routeParameters))
        ->assertSessionHas('success', 'El período fue creado correctamente.');

    $period = Periods::query()->where('period', '2026A')->firstOrFail();
    expect($period->year)->toBe(2026);
    expect($period->start_date->toDateString())->toBe('2026-01-15');
    expect($period->end_date->toDateString())->toBe('2026-06-30');
    expect($period->is_active)->toBeTrue();

    $this->get(route('periods.index', [...$routeParameters, 'search' => '2026A']))
        ->assertOk()
        ->assertSee('2026A')
        ->assertSee('15/01/2026');

    $this->get(route('periods.edit', [...$routeParameters, 'periods' => $period]))
        ->assertOk()
        ->assertSee('2026A');

    $this->put(route('periods.update', [...$routeParameters, 'periods' => $period]), [
        'year' => 2026,
        'period' => '2026B',
        'start_date' => '',
        'end_date' => '',
        'is_active' => false,
    ])->assertRedirect(route('periods.index', $routeParameters))
        ->assertSessionHas('success', 'El período fue actualizado correctamente.');

    $this->assertDatabaseHas('periods', [
        'id' => $period->id,
        'year' => 2026,
        'period' => '2026B',
        'start_date' => null,
        'end_date' => null,
        'is_active' => false,
    ]);

    $this->delete(route('periods.destroy', [...$routeParameters, 'periods' => $period]))
        ->assertRedirect(route('periods.index', $routeParameters))
        ->assertSessionHas('success', 'El período fue eliminado correctamente.');
    $this->assertModelMissing($period);
});

test('periods are ordered newest first by year and period code', function () {
    $user = User::factory()->create();

    Periods::factory()->create(['year' => 2025, 'period' => '2025A']);
    Periods::factory()->create(['year' => 2026, 'period' => '2026A']);
    Periods::factory()->create(['year' => 2025, 'period' => '2025B']);
    Periods::factory()->create(['year' => 2026, 'period' => '2026B']);

    $response = $this->actingAs($user)
        ->get(route('periods.index', ['current_team' => $user->currentTeam->slug]))
        ->assertOk();

    expect($response->viewData('periods')->getCollection()->pluck('period')->all())
        ->toBe(['2026B', '2026A', '2025B', '2025A']);
});

test('period search is retained across paginated results', function () {
    $user = User::factory()->create();

    foreach (range(2018, 2025) as $year) {
        Periods::factory()->create(['year' => $year, 'period' => $year.'A']);
        Periods::factory()->create(['year' => $year, 'period' => $year.'B']);
    }

    Periods::factory()->create(['year' => 2026, 'period' => '2026A']);
    Periods::factory()->create(['year' => 2026, 'period' => '2026B']);

    $response = $this->actingAs($user)->get(route('periods.index', [
        'current_team' => $user->currentTeam->slug,
        'search' => '20',
        'page' => 2,
    ]));

    $response
        ->assertOk()
        ->assertSee('18 registros')
        ->assertSee('2018B')
        ->assertSee('2018A')
        ->assertSee('search=20');
    expect($response->viewData('periods')->getCollection()->pluck('period')->all())
        ->toBe(['2019A', '2018B', '2018A']);
});

test('period validation requires a matching year and code and an ordered pair of dates', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $this->actingAs($user);

    $this->from(route('periods.create', $routeParameters))
        ->post(route('periods.store', $routeParameters), [
            'year' => 2026,
            'period' => '2025A',
            'start_date' => '2026-06-30',
            'end_date' => '2026-01-01',
            'is_active' => true,
        ])
        ->assertRedirect(route('periods.create', $routeParameters))
        ->assertInvalid(['period', 'end_date']);

    $this->from(route('periods.create', $routeParameters))
        ->post(route('periods.store', $routeParameters), [
            'year' => 2026,
            'period' => '2026B',
            'start_date' => '2026-07-01',
            'end_date' => '',
            'is_active' => true,
        ])
        ->assertRedirect(route('periods.create', $routeParameters))
        ->assertInvalid(['end_date']);

    $this->assertDatabaseCount('periods', 0);
});

test('period seeder creates the requested records idempotently without dates', function () {
    app(PeriodsSeeder::class)->run();
    app(PeriodsSeeder::class)->run();

    $this->assertDatabaseCount('periods', 2);
    $this->assertDatabaseHas('periods', [
        'year' => 2026,
        'period' => '2026A',
        'start_date' => null,
        'end_date' => null,
        'is_active' => true,
    ]);
    $this->assertDatabaseHas('periods', [
        'year' => 2026,
        'period' => '2026B',
        'start_date' => null,
        'end_date' => null,
        'is_active' => true,
    ]);
});
