<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIntentionRequest;
use App\Http\Requests\UpdateIntentionRequest;
use App\Models\Intention;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IntentionController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim($validated['search'] ?? '');
        $records = Intention::query()
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('question-bank.index', [
            'resourceTitle' => 'Intenciones',
            'resourceDescription' => 'Define el propósito que persigue cada evaluación.',
            'records' => $records,
            'columns' => [
                ['label' => 'Nombre', 'field' => 'name'],
                ['label' => 'Descripción', 'field' => 'description'],
                ['label' => 'Estado', 'field' => 'is_active', 'type' => 'status'],
            ],
            'indexRoute' => 'intentions.index',
            'createRoute' => 'intentions.create',
            'editRoute' => 'intentions.edit',
            'deleteRoute' => 'intentions.destroy',
            'routeParameter' => 'intention',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return $this->formView(null);
    }

    public function store(StoreIntentionRequest $request): RedirectResponse
    {
        Intention::query()->create($request->validated());

        return redirect()->route('intentions.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La intención fue creada correctamente.');
    }

    public function edit(string $currentTeam, Intention $intention): View
    {
        return $this->formView($intention);
    }

    public function update(UpdateIntentionRequest $request, string $currentTeam, Intention $intention): RedirectResponse
    {
        $intention->update($request->validated());

        return redirect()->route('intentions.index', ['current_team' => $currentTeam])
            ->with('success', 'La intención fue actualizada correctamente.');
    }

    public function destroy(string $currentTeam, Intention $intention): RedirectResponse
    {
        if ($intention->evaluationInstruments()->exists()) {
            return redirect()->route('intentions.index', ['current_team' => $currentTeam])
                ->with('error', 'No se puede eliminar una intención asociada a instrumentos.');
        }

        $intention->delete();

        return redirect()->route('intentions.index', ['current_team' => $currentTeam])
            ->with('success', 'La intención fue eliminada correctamente.');
    }

    private function formView(?Intention $record): View
    {
        return view('question-bank.form', [
            'resourceTitle' => 'intención',
            'record' => $record,
            'fields' => [
                ['name' => 'name', 'label' => 'Nombre', 'required' => true, 'max' => 255],
                ['name' => 'description', 'label' => 'Descripción', 'type' => 'textarea'],
                ['name' => 'is_active', 'label' => 'Intención activa', 'type' => 'checkbox'],
            ],
            'storeRoute' => 'intentions.store',
            'updateRoute' => 'intentions.update',
            'indexRoute' => 'intentions.index',
            'routeParameter' => 'intention',
        ]);
    }
}
