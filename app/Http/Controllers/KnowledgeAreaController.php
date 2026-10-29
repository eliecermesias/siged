<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKnowledgeAreaRequest;
use App\Http\Requests\UpdateKnowledgeAreaRequest;
use App\Models\KnowledgeArea;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KnowledgeAreaController extends Controller
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
        $knowledgeAreas = KnowledgeArea::query()
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('knowledge-areas.index', [
            'knowledgeAreas' => $knowledgeAreas,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('knowledge-areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKnowledgeAreaRequest $request): RedirectResponse
    {
        KnowledgeArea::query()->create($request->validated());

        return redirect()
            ->route('knowledge-areas.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'El área del conocimiento fue creada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $currentTeam, KnowledgeArea $knowledgeArea): View
    {
        return view('knowledge-areas.edit', ['knowledgeArea' => $knowledgeArea]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKnowledgeAreaRequest $request, string $currentTeam, KnowledgeArea $knowledgeArea): RedirectResponse
    {
        $knowledgeArea->update($request->validated());

        return redirect()
            ->route('knowledge-areas.index', ['current_team' => $currentTeam])
            ->with('success', 'El área del conocimiento fue actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currentTeam, KnowledgeArea $knowledgeArea): RedirectResponse
    {
        if ($knowledgeArea->academicPrograms()->exists()) {
            return redirect()
                ->route('knowledge-areas.index', ['current_team' => $currentTeam])
                ->with('error', 'No se puede eliminar el área porque tiene programas académicos asociados.');
        }

        $knowledgeArea->delete();

        return redirect()
            ->route('knowledge-areas.index', ['current_team' => $currentTeam])
            ->with('success', 'El área del conocimiento fue eliminada correctamente.');
    }
}
