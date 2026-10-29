<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicProgramsRequest;
use App\Http\Requests\UpdateAcademicProgramsRequest;
use App\Models\AcademicPrograms;
use App\Models\EducationLevel;
use App\Models\KnowledgeArea;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcademicProgramsController extends Controller
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
        $academicPrograms = AcademicPrograms::query()
            ->with(['educationLevel:id,name', 'knowledgeArea:id,name'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('snies', 'like', "%{$search}%")
                        ->orWhereHas('educationLevel', fn (Builder $levelQuery) => $levelQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('knowledgeArea', fn (Builder $areaQuery) => $areaQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('academic-programs.index', [
            'academicPrograms' => $academicPrograms,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('academic-programs.create', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicProgramsRequest $request): RedirectResponse
    {
        AcademicPrograms::query()->create($request->validated());

        return redirect()
            ->route('academic-programs.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'El programa académico fue creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $currentTeam, AcademicPrograms $academicPrograms): View
    {
        return view('academic-programs.edit', [
            ...$this->formOptions(),
            'academicProgram' => $academicPrograms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicProgramsRequest $request, string $currentTeam, AcademicPrograms $academicPrograms): RedirectResponse
    {
        $academicPrograms->update($request->validated());

        return redirect()
            ->route('academic-programs.index', ['current_team' => $currentTeam])
            ->with('success', 'El programa académico fue actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currentTeam, AcademicPrograms $academicPrograms): RedirectResponse
    {
        $academicPrograms->delete();

        return redirect()
            ->route('academic-programs.index', ['current_team' => $currentTeam])
            ->with('success', 'El programa académico fue eliminado correctamente.');
    }

    /**
     * Get the reference data for academic program forms.
     *
     * @return array{educationLevels: Collection<int, EducationLevel>, knowledgeAreas: Collection<int, KnowledgeArea>}
     */
    private function formOptions(): array
    {
        return [
            'educationLevels' => EducationLevel::query()->orderBy('name')->get(['id', 'name']),
            'knowledgeAreas' => KnowledgeArea::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
