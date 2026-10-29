<?php

namespace App\Http\Controllers;

use App\Actions\Teachers\CreateTeacher;
use App\Http\Requests\StoreTeachersRequest;
use App\Http\Requests\UpdateTeachersRequest;
use App\Models\People;
use App\Models\Teachers;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeachersController extends Controller
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

        $teachers = Teachers::query()
            ->with('person:id,first_name,last_name,document,email')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhereHas('person', function (Builder $personQuery) use ($search): void {
                            $personQuery->whereAny(
                                ['first_name', 'last_name', 'document', 'email'],
                                'like',
                                "%{$search}%",
                            );
                        });
                });
            })
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return view('teachers.index', [
            'teachers' => $teachers,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeachersRequest $request, CreateTeacher $createTeacher): RedirectResponse
    {
        $validated = $request->validated();
        $person = People::query()->findOrFail($validated['person_id']);

        $createTeacher->assign($person);

        return redirect()
            ->route('teachers.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'El docente fue creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $currentTeam, Teachers $teachers): View
    {
        return view('teachers.edit', [
            'teacher' => $teachers,
            'people' => $this->peopleOptions($teachers),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeachersRequest $request, string $currentTeam, Teachers $teachers): RedirectResponse
    {
        $teachers->update($request->validated());

        return redirect()
            ->route('teachers.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La información del docente fue actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currentTeam, Teachers $teachers): RedirectResponse
    {
        $teachers->delete();

        return redirect()
            ->route('teachers.index', ['current_team' => request()->route('current_team')])
            ->with('success', 'El docente fue eliminado correctamente.');
    }

    /**
     * Get the people available for teacher forms.
     *
     * @return Collection<int, People>
     */
    private function peopleOptions(?Teachers $teacher = null): Collection
    {
        return People::query()
            ->where(function (Builder $query) use ($teacher): void {
                $query->whereDoesntHave('teacher');

                if ($teacher !== null) {
                    $query->orWhereKey($teacher->person_id);
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'document']);
    }
}
