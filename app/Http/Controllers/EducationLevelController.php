<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEducationLevelRequest;
use App\Http\Requests\UpdateEducationLevelRequest;
use App\Models\EducationLevel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EducationLevelController extends Controller
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
        $educationLevels = EducationLevel::query()
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('education-levels.index', [
            'educationLevels' => $educationLevels,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('education-levels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEducationLevelRequest $request): RedirectResponse
    {
        EducationLevel::query()->create($request->validated());

        return redirect()
            ->route('education-levels.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'El nivel de formación fue creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $currentTeam, EducationLevel $educationLevel): View
    {
        return view('education-levels.edit', ['educationLevel' => $educationLevel]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEducationLevelRequest $request, string $currentTeam, EducationLevel $educationLevel): RedirectResponse
    {
        $educationLevel->update($request->validated());

        return redirect()
            ->route('education-levels.index', ['current_team' => $currentTeam])
            ->with('success', 'El nivel de formación fue actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currentTeam, EducationLevel $educationLevel): RedirectResponse
    {
        if ($educationLevel->academicPrograms()->exists()) {
            return redirect()
                ->route('education-levels.index', ['current_team' => $currentTeam])
                ->with('error', 'No se puede eliminar el nivel porque tiene programas académicos asociados.');
        }

        $educationLevel->delete();

        return redirect()
            ->route('education-levels.index', ['current_team' => $currentTeam])
            ->with('success', 'El nivel de formación fue eliminado correctamente.');
    }
}
