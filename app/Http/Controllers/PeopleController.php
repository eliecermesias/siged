<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeopleRequest;
use App\Http\Requests\UpdatePeopleRequest;
use App\Models\DocumentTypes;
use App\Models\People;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PeopleController extends Controller
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

        $people = People::query()
            ->with([
                'documentType:id,name',
                'teacher:id,person_id',
                'student:id,person_id',
                'coordinator:id,person_id',
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->whereAny(
                    ['first_name', 'last_name', 'document', 'email', 'phone'],
                    'like',
                    "%{$search}%",
                );
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('people.index', [
            'people' => $people,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('people.create', [
            'documentTypes' => DocumentTypes::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeopleRequest $request): RedirectResponse
    {
        People::create($request->validated());

        return redirect()
            ->route('people.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La persona fue creada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $currentTeam, string $people): View
    {
        $person = People::query()->findOrFail($people);

        return view('people.edit', [
            'person' => $person,
            'documentTypes' => DocumentTypes::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePeopleRequest $request, string $currentTeam, string $people): RedirectResponse
    {
        People::query()->findOrFail($people)->update($request->validated());

        return redirect()
            ->route('people.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La información de la persona fue actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currentTeam, string $people): RedirectResponse
    {
        People::query()->findOrFail($people)->delete();

        return redirect()
            ->route('people.index', ['current_team' => request()->route('current_team')])
            ->with('success', 'La persona fue eliminada correctamente.');
    }
}
