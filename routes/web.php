<?php

use App\Http\Controllers\AcademicProgramsController;
use App\Http\Controllers\CharacteristicsController;
use App\Http\Controllers\CoordinatorsController;
use App\Http\Controllers\EducationLevelController;
use App\Http\Controllers\EvaluationInstrumentController;
use App\Http\Controllers\EvaluationModalityController;
use App\Http\Controllers\InstrumentVersionController;
use App\Http\Controllers\IntentionController;
use App\Http\Controllers\KnowledgeAreaController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\PeriodsController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->get('personas', function (Request $request): RedirectResponse {
    return redirect()->route('people.index', [
        'current_team' => $request->user()->currentTeam->slug,
    ]);
})->name('people.legacy');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::resource('personas', PeopleController::class)
            ->except('show')
            ->parameters(['personas' => 'people'])
            ->names('people');
        Route::resource('docentes', TeachersController::class)
            ->except('show')
            ->parameters(['docentes' => 'teachers'])
            ->names('teachers');
        Route::resource('estudiantes', StudentsController::class)
            ->except('show')
            ->parameters(['estudiantes' => 'students'])
            ->names('students');
        Route::resource('coordinadores', CoordinatorsController::class)
            ->except('show')
            ->parameters(['coordinadores' => 'coordinators'])
            ->names('coordinators');
        Route::resource('programas-academicos', AcademicProgramsController::class)
            ->except('show')
            ->parameters(['programas-academicos' => 'academicPrograms'])
            ->names('academic-programs');
        Route::resource('niveles-formacion', EducationLevelController::class)
            ->except('show')
            ->parameters(['niveles-formacion' => 'educationLevel'])
            ->names('education-levels');
        Route::resource('areas-conocimiento', KnowledgeAreaController::class)
            ->except('show')
            ->parameters(['areas-conocimiento' => 'knowledgeArea'])
            ->names('knowledge-areas');
        Route::resource('periodos', PeriodsController::class)
            ->except('show')
            ->parameters(['periodos' => 'periods'])
            ->names('periods');
        Route::resource('intenciones', IntentionController::class)
            ->except('show')
            ->parameters(['intenciones' => 'intention'])
            ->names('intentions');
        Route::resource('modalidades-evaluacion', EvaluationModalityController::class)
            ->except('show')
            ->parameters(['modalidades-evaluacion' => 'evaluationModality'])
            ->names('evaluation-modalities');
        Route::resource('instrumentos-evaluacion', EvaluationInstrumentController::class)
            ->except('show')
            ->parameters(['instrumentos-evaluacion' => 'evaluationInstrument'])
            ->names('evaluation-instruments');
        Route::resource('versiones-instrumentos', InstrumentVersionController::class)
            ->except('show')
            ->parameters(['versiones-instrumentos' => 'instrumentVersion'])
            ->names('instrument-versions');
        Route::resource('caracteristicas', CharacteristicsController::class)
            ->except('show')
            ->parameters(['caracteristicas' => 'characteristic'])
            ->names('characteristics');
        Route::resource('preguntas', QuestionController::class)
            ->except('show')
            ->parameters(['preguntas' => 'question'])
            ->names('questions');
    });

require __DIR__.'/settings.php';
