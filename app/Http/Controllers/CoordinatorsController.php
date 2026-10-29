<?php

namespace App\Http\Controllers;

use App\Actions\Coordinators\CreateCoordinator;
use App\Http\Requests\StoreCoordinatorsRequest;
use App\Http\Requests\UpdateCoordinatorsRequest;
use App\Models\Coordinators;
use App\Models\People;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CoordinatorsController extends Controller
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

        $coordinators = Coordinators::query()
            ->with('person:id,first_name,last_name,document,email')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhereHas('person', function (Builder $personQuery) use ($search): void {
                            $personQuery->whereAny(
                                ['first_name', 'last_name', 'document', 'email'],
                                'like',
                                "%{$search}%",
                            );
                        });
                });
            })
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return view('coordinators.index', [
            'coordinators' => $coordinators,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('coordinators.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCoordinatorsRequest $request, CreateCoordinator $createCoordinator): RedirectResponse
    {
        $validated = $request->validated();
        $person = People::query()->findOrFail($validated['person_id']);

        $createCoordinator->assign($person);

        return redirect()
            ->route('coordinators.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'El coordinador fue creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $currentTeam, Coordinators $coordinators): View
    {
        return view('coordinators.edit', [
            'coordinator' => $coordinators,
            'people' => $this->peopleOptions($coordinators),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCoordinatorsRequest $request, string $currentTeam, Coordinators $coordinators): RedirectResponse
    {
        $coordinators->update($request->validated());

        return redirect()
            ->route('coordinators.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La información del coordinador fue actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currentTeam, Coordinators $coordinators): RedirectResponse
    {
        $coordinators->delete();

        return redirect()
            ->route('coordinators.index', ['current_team' => request()->route('current_team')])
            ->with('success', 'El coordinador fue eliminado correctamente.');
    }

    /**
     * Get the people available for coordinator forms.
     *
     * @return Collection<int, People>
     */
    private function peopleOptions(?Coordinators $coordinator = null): Collection
    {
        return People::query()
            ->where(function (Builder $query) use ($coordinator): void {
                $query->whereDoesntHave('coordinator');

                if ($coordinator !== null) {
                    $query->orWhereKey($coordinator->person_id);
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'document']);
    }
}
