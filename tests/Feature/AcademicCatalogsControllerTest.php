<?php

use App\Models\AcademicPrograms;
use App\Models\EducationLevel;
use App\Models\KnowledgeArea;
use App\Models\User;
use Database\Seeders\AcademicProgramsSeeder;
use Database\Seeders\EducationLevelSeeder;
use Database\Seeders\KnowledgeAreaSeeder;

test('authenticated team members can manage education levels and knowledge areas', function () {
    $user = User::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $educationLevel = EducationLevel::factory()->create(['name' => 'Pregrado']);
    $knowledgeArea = KnowledgeArea::factory()->create(['name' => 'Ciencias Naturales']);
    $this->actingAs($user);

    $this->get(route('education-levels.index', [...$routeParameters, 'search' => 'Pregrado']))
        ->assertOk()
        ->assertSee('Pregrado');
    $this->get(route('knowledge-areas.index', [...$routeParameters, 'search' => 'Naturales']))
        ->assertOk()
        ->assertSee('Ciencias Naturales');

    $this->post(route('education-levels.store', $routeParameters), [
        'name' => 'Especialización',
        'description' => 'Estudios de posgrado',
        'is_active' => true,
    ])->assertRedirect(route('education-levels.index', $routeParameters));

    $createdLevel = EducationLevel::query()->where('name', 'Especialización')->firstOrFail();
    expect($createdLevel->is_active)->toBeTrue();

    $this->put(route('education-levels.update', [...$routeParameters, 'educationLevel' => $createdLevel]), [
        'name' => 'Maestría',
        'description' => '',
        'is_active' => false,
    ])->assertRedirect(route('education-levels.index', $routeParameters));

    $this->post(route('knowledge-areas.store', $routeParameters), [
        'name' => 'Agronomía',
        'description' => '',
        'is_active' => true,
    ])->assertRedirect(route('knowledge-areas.index', $routeParameters));

    $createdArea = KnowledgeArea::query()->where('name', 'Agronomía')->firstOrFail();
    $this->put(route('knowledge-areas.update', [...$routeParameters, 'knowledgeArea' => $createdArea]), [
        'name' => 'Bellas Artes',
        'description' => 'Área artística',
        'is_active' => true,
    ])->assertRedirect(route('knowledge-areas.index', $routeParameters));

    $this->delete(route('education-levels.destroy', [...$routeParameters, 'educationLevel' => $educationLevel]))
        ->assertRedirect(route('education-levels.index', $routeParameters));
    $this->delete(route('knowledge-areas.destroy', [...$routeParameters, 'knowledgeArea' => $knowledgeArea]))
        ->assertRedirect(route('knowledge-areas.index', $routeParameters));

    $this->assertDatabaseMissing('education_levels', ['id' => $educationLevel->id]);
    $this->assertDatabaseMissing('knowledge_areas', ['id' => $knowledgeArea->id]);
    $this->assertDatabaseHas('education_levels', ['id' => $createdLevel->id, 'name' => 'Maestría', 'is_active' => false]);
    $this->assertDatabaseHas('knowledge_areas', ['id' => $createdArea->id, 'name' => 'Bellas Artes']);
});

test('academic programs use existing catalogs and support searching, editing, and deletion', function () {
    $user = User::factory()->create();
    $educationLevel = EducationLevel::factory()->create(['name' => 'Universitario']);
    $updatedEducationLevel = EducationLevel::factory()->create(['name' => 'Tecnológica']);
    $knowledgeArea = KnowledgeArea::factory()->create(['name' => 'Ingeniería/Arquitectura']);
    $updatedKnowledgeArea = KnowledgeArea::factory()->create(['name' => 'Ciencias de la Salud']);
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $this->actingAs($user);

    $this->get(route('academic-programs.create', $routeParameters))
        ->assertOk()
        ->assertSee('Universitario')
        ->assertSee('Ingeniería/Arquitectura');

    $this->post(route('academic-programs.store', $routeParameters), [
        'education_level_id' => $educationLevel->id,
        'knowledge_area_id' => $knowledgeArea->id,
        'name' => 'Ingeniería de Sistemas',
        'description' => '',
        'snies' => '53735',
        'resolution' => '',
        'is_active' => true,
    ])->assertRedirect(route('academic-programs.index', $routeParameters));

    $program = AcademicPrograms::query()->where('snies', '53735')->firstOrFail();
    expect($program->educationLevel->is($educationLevel))->toBeTrue();
    expect($program->knowledgeArea->is($knowledgeArea))->toBeTrue();
    expect($program->is_active)->toBeTrue();

    $this->get(route('academic-programs.index', [...$routeParameters, 'search' => '53735']))
        ->assertOk()
        ->assertSee('Ingeniería de Sistemas');

    $this->get(route('academic-programs.edit', [...$routeParameters, 'academicPrograms' => $program]))
        ->assertOk()
        ->assertSee('Ingeniería de Sistemas');

    $this->put(route('academic-programs.update', [...$routeParameters, 'academicPrograms' => $program]), [
        'education_level_id' => $updatedEducationLevel->id,
        'knowledge_area_id' => $updatedKnowledgeArea->id,
        'name' => 'Ingeniería Industrial',
        'description' => '',
        'snies' => '90614',
        'resolution' => '2025-001',
        'is_active' => false,
    ])->assertRedirect(route('academic-programs.index', $routeParameters));

    $this->assertDatabaseHas('academic_programs', [
        'id' => $program->id,
        'education_level_id' => $updatedEducationLevel->id,
        'knowledge_area_id' => $updatedKnowledgeArea->id,
        'name' => 'Ingeniería Industrial',
        'snies' => '90614',
        'is_active' => false,
    ]);

    $this->delete(route('academic-programs.destroy', [...$routeParameters, 'academicPrograms' => $program]))
        ->assertRedirect(route('academic-programs.index', $routeParameters));
    $this->assertModelMissing($program);
});

test('catalog pagination retains the active search query', function () {
    $user = User::factory()->create();

    foreach (range(1, 16) as $number) {
        EducationLevel::factory()->create([
            'name' => sprintf('Nivel buscado %02d', $number),
        ]);
    }

    $this->actingAs($user)
        ->get(route('education-levels.index', [
            'current_team' => $user->currentTeam->slug,
            'search' => 'Nivel buscado',
            'page' => 2,
        ]))
        ->assertOk()
        ->assertSee('16 registros')
        ->assertSee('Nivel buscado 16')
        ->assertDontSee('Nivel buscado 01')
        ->assertSee('search=Nivel');
});

test('catalog names and program SNIES codes must be unique and foreign keys must exist', function () {
    $user = User::factory()->create();
    $educationLevel = EducationLevel::factory()->create(['name' => 'Universitario']);
    $knowledgeArea = KnowledgeArea::factory()->create(['name' => 'Ingeniería/Arquitectura']);
    $program = AcademicPrograms::factory()
        ->for($educationLevel, 'educationLevel')
        ->for($knowledgeArea, 'knowledgeArea')
        ->create(['snies' => '123456']);
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $this->actingAs($user);

    $this->from(route('education-levels.create', $routeParameters))
        ->post(route('education-levels.store', $routeParameters), [
            'name' => $educationLevel->name,
            'description' => '',
            'is_active' => true,
        ])
        ->assertRedirect(route('education-levels.create', $routeParameters))
        ->assertInvalid(['name']);

    $this->from(route('academic-programs.create', $routeParameters))
        ->post(route('academic-programs.store', $routeParameters), [
            'education_level_id' => 999999,
            'knowledge_area_id' => $knowledgeArea->id,
            'name' => 'Programa inválido',
            'description' => '',
            'snies' => $program->snies,
            'resolution' => '',
            'is_active' => true,
        ])
        ->assertRedirect(route('academic-programs.create', $routeParameters))
        ->assertInvalid(['education_level_id', 'snies']);

    $this->assertDatabaseCount('academic_programs', 1);
});

test('education levels and knowledge areas with programs cannot be deleted', function () {
    $user = User::factory()->create();
    $program = AcademicPrograms::factory()->create();
    $routeParameters = ['current_team' => $user->currentTeam->slug];
    $this->actingAs($user);

    $this->delete(route('education-levels.destroy', [
        ...$routeParameters,
        'educationLevel' => $program->education_level_id,
    ]))
        ->assertRedirect(route('education-levels.index', $routeParameters))
        ->assertSessionHas('error');
    $this->delete(route('knowledge-areas.destroy', [
        ...$routeParameters,
        'knowledgeArea' => $program->knowledge_area_id,
    ]))
        ->assertRedirect(route('knowledge-areas.index', $routeParameters))
        ->assertSessionHas('error');

    $this->assertModelExists($program);
});

test('academic catalog seeders create the requested catalogs idempotently', function () {
    app(EducationLevelSeeder::class)->run();
    app(KnowledgeAreaSeeder::class)->run();
    app(AcademicProgramsSeeder::class)->run();
    app(EducationLevelSeeder::class)->run();
    app(KnowledgeAreaSeeder::class)->run();
    app(AcademicProgramsSeeder::class)->run();

    $this->assertDatabaseCount('education_levels', 6);
    $this->assertDatabaseCount('knowledge_areas', 8);
    $this->assertDatabaseCount('academic_programs', 15);

    foreach (['Técnica profesional', 'Tecnológica', 'Universitario', 'Especialización', 'Maestría', 'Doctorado'] as $name) {
        $this->assertDatabaseHas('education_levels', ['name' => $name]);
    }

    foreach ([
        'Agronomía',
        'Bellas Artes',
        'Ciencias de la Educación',
        'Ciencias de la Salud',
        'Ciencias Sociales y Humanas',
        'Economía/Administración/Contaduría',
        'Ingeniería/Arquitectura',
        'Matemáticas/Ciencias Naturales',
    ] as $name) {
        $this->assertDatabaseHas('knowledge_areas', ['name' => $name]);
    }

    $this->assertDatabaseHas('academic_programs', [
        'name' => 'Tecnología en Desarrollo de Software',
        'snies' => '104257',
        'is_active' => true,
    ]);

    $program = AcademicPrograms::query()->where('snies', '104257')->firstOrFail();
    expect($program->educationLevel->name)->toBe('Tecnológica');
    expect($program->knowledgeArea->name)->toBe('Ingeniería/Arquitectura');
});
