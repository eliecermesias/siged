<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluationModalityRequest;
use App\Http\Requests\UpdateEvaluationModalityRequest;
use App\Models\EvaluationModality;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EvaluationModalityController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim($validated['search'] ?? '');
        $records = EvaluationModality::query()
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('question-bank.index', [
            'resourceTitle' => 'Modalidades de evaluación',
            'resourceDescription' => 'Define quién evalúa a quién: autoevaluación, heteroevaluación o coevaluación.',
            'records' => $records,
            'columns' => [
                ['label' => 'Nombre', 'field' => 'name'],
                ['label' => 'Descripción', 'field' => 'description'],
                ['label' => 'Estado', 'field' => 'is_active', 'type' => 'status'],
            ],
            'indexRoute' => 'evaluation-modalities.index',
            'createRoute' => 'evaluation-modalities.create',
            'editRoute' => 'evaluation-modalities.edit',
            'deleteRoute' => 'evaluation-modalities.destroy',
            'routeParameter' => 'evaluationModality',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return $this->formView(null);
    }

    public function store(StoreEvaluationModalityRequest $request): RedirectResponse
    {
        EvaluationModality::query()->create($request->validated());

        return redirect()->route('evaluation-modalities.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La modalidad fue creada correctamente.');
    }

    public function edit(string $currentTeam, EvaluationModality $evaluationModality): View
    {
        return $this->formView($evaluationModality);
    }

    public function update(UpdateEvaluationModalityRequest $request, string $currentTeam, EvaluationModality $evaluationModality): RedirectResponse
    {
        $evaluationModality->update($request->validated());

        return redirect()->route('evaluation-modalities.index', ['current_team' => $currentTeam])
            ->with('success', 'La modalidad fue actualizada correctamente.');
    }

    public function destroy(string $currentTeam, EvaluationModality $evaluationModality): RedirectResponse
    {
        if ($evaluationModality->evaluationInstruments()->exists()) {
            return redirect()->route('evaluation-modalities.index', ['current_team' => $currentTeam])
                ->with('error', 'No se puede eliminar una modalidad asociada a instrumentos.');
        }

        $evaluationModality->delete();

        return redirect()->route('evaluation-modalities.index', ['current_team' => $currentTeam])
            ->with('success', 'La modalidad fue eliminada correctamente.');
    }

    private function formView(?EvaluationModality $record): View
    {
        return view('question-bank.form', [
            'resourceTitle' => 'modalidad',
            'record' => $record,
            'fields' => [
                ['name' => 'name', 'label' => 'Nombre', 'required' => true, 'max' => 255],
                ['name' => 'description', 'label' => 'Descripción', 'type' => 'textarea'],
                ['name' => 'is_active', 'label' => 'Modalidad activa', 'type' => 'checkbox'],
            ],
            'storeRoute' => 'evaluation-modalities.store',
            'updateRoute' => 'evaluation-modalities.update',
            'indexRoute' => 'evaluation-modalities.index',
            'routeParameter' => 'evaluationModality',
        ]);
    }
}
