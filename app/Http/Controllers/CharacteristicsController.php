<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCharacteristicsRequest;
use App\Http\Requests\UpdateCharacteristicsRequest;
use App\Models\Characteristics;
use App\Models\InstrumentVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CharacteristicsController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim($validated['search'] ?? '');
        $records = Characteristics::query()
            ->with('instrumentVersion.evaluationInstrument:id,name')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('instrumentVersion.evaluationInstrument', fn (Builder $related) => $related->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('question-bank.index', [
            'resourceTitle' => 'Características',
            'resourceDescription' => 'Agrupa las preguntas por dimensión dentro de una versión del instrumento.',
            'records' => $records,
            'columns' => [
                ['label' => 'Nombre', 'field' => 'name'],
                ['label' => 'Instrumento', 'field' => 'instrumentVersion.evaluationInstrument.name'],
                ['label' => 'Versión', 'field' => 'instrumentVersion.version_number'],
                ['label' => 'Orden', 'field' => 'position'],
                ['label' => 'Estado', 'field' => 'is_active', 'type' => 'status'],
            ],
            'indexRoute' => 'characteristics.index',
            'createRoute' => 'characteristics.create',
            'editRoute' => 'characteristics.edit',
            'deleteRoute' => 'characteristics.destroy',
            'routeParameter' => 'characteristic',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return $this->formView(null);
    }

    public function store(StoreCharacteristicsRequest $request): RedirectResponse
    {
        Characteristics::query()->create($request->validated());

        return redirect()->route('characteristics.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La característica fue creada correctamente.');
    }

    public function edit(string $currentTeam, Characteristics $characteristic): View
    {
        return $this->formView($characteristic);
    }

    public function update(UpdateCharacteristicsRequest $request, string $currentTeam, Characteristics $characteristic): RedirectResponse
    {
        $characteristic->update($request->validated());

        return redirect()->route('characteristics.index', ['current_team' => $currentTeam])
            ->with('success', 'La característica fue actualizada correctamente.');
    }

    public function destroy(string $currentTeam, Characteristics $characteristic): RedirectResponse
    {
        if ($characteristic->questions()->exists()) {
            return redirect()->route('characteristics.index', ['current_team' => $currentTeam])
                ->with('error', 'No se puede eliminar una característica que tiene preguntas asociadas.');
        }

        $characteristic->delete();

        return redirect()->route('characteristics.index', ['current_team' => $currentTeam])
            ->with('success', 'La característica fue eliminada correctamente.');
    }

    private function formView(?Characteristics $record): View
    {
        $versions = InstrumentVersion::query()
            ->with('evaluationInstrument:id,name')
            ->orderByDesc('version_number')
            ->get()
            ->mapWithKeys(fn (InstrumentVersion $version): array => [
                $version->id => $version->evaluationInstrument->name.' — v'.$version->version_number,
            ])
            ->all();

        return view('question-bank.form', [
            'resourceTitle' => 'característica',
            'record' => $record,
            'fields' => [
                [
                    'name' => 'instrument_version_id',
                    'label' => 'Versión del instrumento',
                    'type' => 'select',
                    'required' => true,
                    'options' => $versions,
                ],
                ['name' => 'name', 'label' => 'Nombre', 'required' => true, 'max' => 255],
                ['name' => 'description', 'label' => 'Descripción', 'type' => 'textarea'],
                ['name' => 'position', 'label' => 'Orden', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 65535],
                ['name' => 'is_active', 'label' => 'Característica activa', 'type' => 'checkbox'],
            ],
            'storeRoute' => 'characteristics.store',
            'updateRoute' => 'characteristics.update',
            'indexRoute' => 'characteristics.index',
            'routeParameter' => 'characteristic',
        ]);
    }
}
