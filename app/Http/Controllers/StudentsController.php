<?php

namespace App\Http\Controllers;

use App\Actions\Students\CreateStudent;
use App\Http\Requests\StoreStudentsRequest;
use App\Http\Requests\UpdateStudentsRequest;
use App\Models\People;
use App\Models\Students;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudentsController extends Controller
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

        $students = Students::query()
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

        return view('students.index', [
            'students' => $students,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentsRequest $request, CreateStudent $createStudent): RedirectResponse
    {
        $validated = $request->validated();
        $person = People::query()->findOrFail($validated['person_id']);

        $createStudent->assign($person);

        return redirect()
            ->route('students.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'El estudiante fue creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $currentTeam, Students $students): View
    {
        return view('students.edit', [
            'student' => $students,
            'people' => $this->peopleOptions($students),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentsRequest $request, string $currentTeam, Students $students): RedirectResponse
    {
        $students->update($request->validated());

        return redirect()
            ->route('students.index', ['current_team' => $request->route('current_team')])
            ->with('success', 'La información del estudiante fue actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currentTeam, Students $students): RedirectResponse
    {
        $students->delete();

        return redirect()
            ->route('students.index', ['current_team' => request()->route('current_team')])
            ->with('success', 'El estudiante fue eliminado correctamente.');
    }

    /**
     * Get the people available for student forms.
     *
     * @return Collection<int, People>
     */
    private function peopleOptions(?Students $student = null): Collection
    {
        return People::query()
            ->where(function (Builder $query) use ($student): void {
                $query->whereDoesntHave('student');

                if ($student !== null) {
                    $query->orWhereKey($student->person_id);
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'document']);
    }
}
