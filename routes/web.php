<?php

use App\Http\Controllers\PeopleController;
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
    });

require __DIR__.'/settings.php';
