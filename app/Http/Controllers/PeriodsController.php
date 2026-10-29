<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeriodsRequest;
use App\Http\Requests\UpdatePeriodsRequest;
use App\Models\Periods;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PeriodsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $search = trim($validated['search'] ?? '');
        $periods = Periods::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('period', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%")
                        ->orWhere('start_date', 'like', "%{$search}%")
                        ->orWhere('end_date', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('year')
            ->orderByDesc('period')
            ->paginate(15)
            ->withQueryString();

        return view('periods.index', [
            'periods' => $periods,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('periods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeriodsRequest $request): RedirectResponse
    {
        Periods::query()->create($request->validated());

        return redirect()
            ->route('periods.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'El período fue creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $currentTeam, Periods $periods): View
    {
        return view('periods.edit', ['period' => $periods]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePeriodsRequest $request, string $currentTeam, Periods $periods): RedirectResponse
    {
        $periods->update($request->validated());

        return redirect()
            ->route('periods.index', ['current_team' => $currentTeam])
            ->with('success', 'El período fue actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currentTeam, Periods $periods): RedirectResponse
    {
        $periods->delete();

        return redirect()
            ->route('periods.index', ['current_team' => $currentTeam])
            ->with('success', 'El período fue eliminado correctamente.');
    }
}
