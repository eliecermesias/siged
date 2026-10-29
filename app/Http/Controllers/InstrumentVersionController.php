<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstrumentVersionRequest;
use App\Http\Requests\UpdateInstrumentVersionRequest;
use App\Models\EvaluationInstrument;
use App\Models\InstrumentVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InstrumentVersionController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim($validated['search'] ?? '');
        $records = InstrumentVersion::query()
            ->with('evaluationInstrument:id,name')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('version_number', 'like', "%{$search}%")
                    ->orWhereHas('evaluationInstrument', fn (Builder $related) => $related->where('name', 'like', "%{$search}%"));
            })
            ->orderByDesc('evaluation_instrument_id')
            ->orderByDesc('version_number')
            ->paginate(15)
            ->withQueryString();

        return view('question-bank.index', [
            'resourceTitle' => 'Versiones de instrumentos',
            'resourceDescription' => 'Cada versión conserva el conjunto de características y preguntas de un instrumento.',
            'records' => $records,
            'columns' => [
                ['label' => 'Instrumento', 'field' => 'evaluationInstrument.name'],
                ['label' => 'Versión', 'field' => 'version_number'],
                ['label' => 'Notas', 'field' => 'notes'],
                ['label' => 'Estado', 'field' => 'is_active', 'type' => 'status'],
            ],
            'indexRoute' => 'instrument-versions.index',
            'createRoute' => 'instrument-versions.create',
            'editRoute' => 'instrument-versions.edit',
            'deleteRoute' => 'instrument-versions.destroy',
            'routeParameter' => 'instrumentVersion',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return $this->formView(null);
    }

    public function store(StoreInstrumentVersionRequest $request): RedirectResponse
    {
        InstrumentVersion::query()->create($request->validated());

        return redirect()->route('instrument-versions.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La versión fue creada correctamente.');
    }

    public function edit(string $currentTeam, InstrumentVersion $instrumentVersion): View
    {
        return $this->formView($instrumentVersion);
    }

    public function update(UpdateInstrumentVersionRequest $request, string $currentTeam, InstrumentVersion $instrumentVersion): RedirectResponse
    {
        $instrumentVersion->update($request->validated());

        return redirect()->route('instrument-versions.index', ['current_team' => $currentTeam])
            ->with('success', 'La versión fue actualizada correctamente.');
    }

    public function destroy(string $currentTeam, InstrumentVersion $instrumentVersion): RedirectResponse
    {
        if ($instrumentVersion->characteristics()->exists()) {
            return redirect()->route('instrument-versions.index', ['current_team' => $currentTeam])
                ->with('error', 'No se puede eliminar una versión con características asociadas.');
        }

        $instrumentVersion->delete();

        return redirect()->route('instrument-versions.index', ['current_team' => $currentTeam])
            ->with('success', 'La versión fue eliminada correctamente.');
    }

    private function formView(?InstrumentVersion $record): View
    {
        return view('question-bank.form', [
            'resourceTitle' => 'versión de instrumento',
            'record' => $record,
            'fields' => [
                [
                    'name' => 'evaluation_instrument_id',
                    'label' => 'Instrumento',
                    'type' => 'select',
                    'required' => true,
                    'options' => EvaluationInstrument::query()->orderBy('name')->pluck('name', 'id')->all(),
                ],
                ['name' => 'version_number', 'label' => 'Número de versión', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 65535],
                ['name' => 'notes', 'label' => 'Notas', 'type' => 'textarea'],
                ['name' => 'is_active', 'label' => 'Versión activa', 'type' => 'checkbox'],
            ],
            'storeRoute' => 'instrument-versions.store',
            'updateRoute' => 'instrument-versions.update',
            'indexRoute' => 'instrument-versions.index',
            'routeParameter' => 'instrumentVersion',
        ]);
    }
}
