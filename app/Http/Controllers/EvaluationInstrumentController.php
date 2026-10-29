<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluationInstrumentRequest;
use App\Http\Requests\UpdateEvaluationInstrumentRequest;
use App\Models\EvaluationInstrument;
use App\Models\EvaluationModality;
use App\Models\Intention;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EvaluationInstrumentController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim($validated['search'] ?? '');
        $records = EvaluationInstrument::query()
            ->with(['intention:id,name', 'evaluationModality:id,name'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('intention', fn (Builder $related) => $related->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('evaluationModality', fn (Builder $related) => $related->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('question-bank.index', [
            'resourceTitle' => 'Instrumentos de evaluación',
            'resourceDescription' => 'Combina una intención y una modalidad en un instrumento concreto.',
            'records' => $records,
            'columns' => [
                ['label' => 'Nombre', 'field' => 'name'],
                ['label' => 'Intención', 'field' => 'intention.name'],
                ['label' => 'Modalidad', 'field' => 'evaluationModality.name'],
                ['label' => 'Estado', 'field' => 'is_active', 'type' => 'status'],
            ],
            'indexRoute' => 'evaluation-instruments.index',
            'createRoute' => 'evaluation-instruments.create',
            'editRoute' => 'evaluation-instruments.edit',
            'deleteRoute' => 'evaluation-instruments.destroy',
            'routeParameter' => 'evaluationInstrument',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return $this->formView(null);
    }

    public function store(StoreEvaluationInstrumentRequest $request): RedirectResponse
    {
        EvaluationInstrument::query()->create($request->validated());

        return redirect()->route('evaluation-instruments.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'El instrumento fue creado correctamente.');
    }

    public function edit(string $currentTeam, EvaluationInstrument $evaluationInstrument): View
    {
        return $this->formView($evaluationInstrument);
    }

    public function update(UpdateEvaluationInstrumentRequest $request, string $currentTeam, EvaluationInstrument $evaluationInstrument): RedirectResponse
    {
        $evaluationInstrument->update($request->validated());

        return redirect()->route('evaluation-instruments.index', ['current_team' => $currentTeam])
            ->with('success', 'El instrumento fue actualizado correctamente.');
    }

    public function destroy(string $currentTeam, EvaluationInstrument $evaluationInstrument): RedirectResponse
    {
        if ($evaluationInstrument->versions()->exists()) {
            return redirect()->route('evaluation-instruments.index', ['current_team' => $currentTeam])
                ->with('error', 'No se puede eliminar un instrumento que ya tiene versiones.');
        }

        $evaluationInstrument->delete();

        return redirect()->route('evaluation-instruments.index', ['current_team' => $currentTeam])
            ->with('success', 'El instrumento fue eliminado correctamente.');
    }

    private function formView(?EvaluationInstrument $record): View
    {
        return view('question-bank.form', [
            'resourceTitle' => 'instrumento de evaluación',
            'record' => $record,
            'fields' => [
                ['name' => 'name', 'label' => 'Nombre', 'required' => true, 'max' => 255],
                [
                    'name' => 'intention_id',
                    'label' => 'Intención',
                    'type' => 'select',
                    'required' => true,
                    'options' => Intention::query()->orderBy('name')->pluck('name', 'id')->all(),
                ],
                [
                    'name' => 'evaluation_modality_id',
                    'label' => 'Modalidad',
                    'type' => 'select',
                    'required' => true,
                    'options' => EvaluationModality::query()->orderBy('name')->pluck('name', 'id')->all(),
                ],
                ['name' => 'description', 'label' => 'Descripción', 'type' => 'textarea'],
                ['name' => 'is_active', 'label' => 'Instrumento activo', 'type' => 'checkbox'],
            ],
            'storeRoute' => 'evaluation-instruments.store',
            'updateRoute' => 'evaluation-instruments.update',
            'indexRoute' => 'evaluation-instruments.index',
            'routeParameter' => 'evaluationInstrument',
        ]);
    }
}
