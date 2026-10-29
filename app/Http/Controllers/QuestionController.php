<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Characteristics;
use App\Models\Question;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim($validated['search'] ?? '');
        $records = Question::query()
            ->with('characteristics:id,name')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('question_text', 'like', "%{$search}%")
                    ->orWhereHas('characteristics', fn (Builder $related) => $related->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('characteristic_id')
            ->orderBy('position')
            ->paginate(15)
            ->withQueryString();

        return view('question-bank.index', [
            'resourceTitle' => 'Preguntas',
            'resourceDescription' => 'Administra los enunciados, su tipo de respuesta y obligatoriedad.',
            'records' => $records,
            'columns' => [
                ['label' => 'Pregunta', 'field' => 'question_text'],
                ['label' => 'Característica', 'field' => 'characteristics.name'],
                ['label' => 'Tipo de respuesta', 'field' => 'response_type'],
                ['label' => 'Obligatoria', 'field' => 'is_required', 'type' => 'boolean'],
                ['label' => 'Orden', 'field' => 'position'],
                ['label' => 'Estado', 'field' => 'is_active', 'type' => 'status'],
            ],
            'indexRoute' => 'questions.index',
            'createRoute' => 'questions.create',
            'editRoute' => 'questions.edit',
            'deleteRoute' => 'questions.destroy',
            'routeParameter' => 'question',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return $this->formView(null);
    }

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        Question::query()->create($request->validated());

        return redirect()->route('questions.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La pregunta fue creada correctamente.');
    }

    public function edit(string $currentTeam, Question $question): View
    {
        return $this->formView($question);
    }

    public function update(UpdateQuestionRequest $request, string $currentTeam, Question $question): RedirectResponse
    {
        $question->update($request->validated());

        return redirect()->route('questions.index', ['current_team' => $currentTeam])
            ->with('success', 'La pregunta fue actualizada correctamente.');
    }

    public function destroy(string $currentTeam, Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()->route('questions.index', ['current_team' => $currentTeam])
            ->with('success', 'La pregunta fue eliminada correctamente.');
    }

    private function formView(?Question $record): View
    {
        return view('question-bank.form', [
            'resourceTitle' => 'pregunta',
            'record' => $record,
            'fields' => [
                [
                    'name' => 'characteristic_id',
                    'label' => 'Característica',
                    'type' => 'select',
                    'required' => true,
                    'options' => Characteristics::query()->orderBy('name')->pluck('name', 'id')->all(),
                ],
                ['name' => 'question_text', 'label' => 'Enunciado', 'type' => 'textarea', 'required' => true],
                [
                    'name' => 'response_type',
                    'label' => 'Tipo de respuesta',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'likert' => 'Escala de valoración',
                        'yes_no' => 'Sí / No',
                        'open_text' => 'Texto abierto',
                    ],
                ],
                ['name' => 'position', 'label' => 'Orden', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 65535],
                ['name' => 'is_required', 'label' => 'Pregunta obligatoria', 'type' => 'checkbox', 'default' => true],
                ['name' => 'is_active', 'label' => 'Pregunta activa', 'type' => 'checkbox'],
            ],
            'storeRoute' => 'questions.store',
            'updateRoute' => 'questions.update',
            'indexRoute' => 'questions.index',
            'routeParameter' => 'question',
        ]);
    }
}
