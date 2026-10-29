<?php

use App\Actions\Students\CreateStudent;
use App\Models\DocumentTypes;
use App\Models\People;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public string $currentTeam;

    public string $personSearch = '';

    public ?int $personId = null;

    public string $selectedPersonLabel = '';

    public bool $showCreatePersonModal = false;

    public string $firstName = '';

    public string $lastName = '';

    public string $documentTypeId = '';

    public string $document = '';

    public string $email = '';

    public string $phone = '';

    public function mount(string $currentTeam): void
    {
        $this->currentTeam = $currentTeam;
    }

    public function updatedPersonSearch(): void
    {
        $this->personId = null;
        $this->selectedPersonLabel = '';
    }

    public function selectPerson(int $personId): void
    {
        $person = People::query()
            ->whereDoesntHave('student')
            ->findOrFail($personId);

        $this->personId = $person->id;
        $this->selectedPersonLabel = "{$person->first_name} {$person->last_name} — {$person->document}";
        $this->personSearch = '';
        $this->resetValidation('personId');
    }

    public function clearPerson(): void
    {
        $this->personId = null;
        $this->selectedPersonLabel = '';
    }

    public function openCreatePersonModal(): void
    {
        if (trim($this->personSearch) === '' || $this->peopleResults->isNotEmpty()) {
            return;
        }

        $this->resetValidation();
        $this->showCreatePersonModal = true;
    }

    public function createStudent(CreateStudent $createStudent): void
    {
        $validated = $this->validate([
            'personId' => ['required', 'integer', 'exists:people,id', Rule::unique('students', 'person_id')],
        ]);

        $person = People::query()->findOrFail($validated['personId']);

        $createStudent->assign($person, 'personId');

        Flux::toast(variant: 'success', text: 'El estudiante fue creado correctamente.');

        $this->redirectRoute('students.index', ['current_team' => $this->currentTeam], navigate: true);
    }

    public function createPersonAndStudent(CreateStudent $createStudent): void
    {
        $validated = $this->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'documentTypeId' => ['required', 'integer', 'exists:document_types,id'],
            'document' => ['required', 'string', 'max:20', 'unique:people,document'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:people,email'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $createStudent->createWithPerson([
            'document_type_id' => $validated['documentTypeId'],
            'document' => $validated['document'],
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
        ]);

        $this->dispatch('close-modal', name: 'create-student-person');

        Flux::toast(variant: 'success', text: 'La persona y el estudiante fueron creados correctamente.');

        $this->redirectRoute('students.index', ['current_team' => $this->currentTeam], navigate: true);
    }

    /**
     * @return Collection<int, People>
     */
    #[Computed]
    public function peopleResults(): Collection
    {
        $search = trim($this->personSearch);

        if (mb_strlen($search) < 2) {
            return new Collection();
        }

        return People::query()
            ->with('student:id,person_id')
            ->whereAny(
                ['first_name', 'last_name', 'document', 'email', 'phone'],
                'like',
                "%{$search}%",
            )
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'document', 'email']);
    }

    /**
     * @return Collection<int, DocumentTypes>
     */
    #[Computed]
    public function documentTypes(): Collection
    {
        return DocumentTypes::query()->orderBy('name')->get(['id', 'name']);
    }
};
?>

<div>
    <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_70px_-45px_rgba(15,23,42,0.45)] dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
        <div class="border-b border-slate-200 px-6 py-6 sm:px-8 sm:py-7 dark:border-unicuces-blue-300/20">
            <flux:heading size="lg">Agregar estudiante</flux:heading>
            <flux:subheading>Busca una persona registrada o crea una persona nueva para registrarla como estudiante.</flux:subheading>
        </div>

        <form wire:submit="createStudent" class="space-y-8 px-6 py-7 sm:px-8 sm:py-8">
            <flux:field>
                <flux:label for="person-search">Buscar persona</flux:label>
                @if ($personId)
                    <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-4 py-3 dark:border-unicuces-blue-300/25">
                        <span class="font-medium text-slate-900 dark:text-white">{{ $selectedPersonLabel }}</span>
                        <flux:button
                            type="button"
                            variant="ghost"
                            size="sm"
                            icon="x-mark"
                            aria-label="Cambiar persona"
                            wire:click="clearPerson"
                        />
                    </div>
                @else
                    <flux:input
                        id="person-search"
                        wire:model.live.debounce.300ms="personSearch"
                        type="search"
                        icon="magnifying-glass"
                        placeholder="Escribe nombre, documento, correo o teléfono"
                        autocomplete="off"
                        aria-label="Buscar persona para registrar como estudiante"
                    />
                    <flux:error name="personId" />

                    @if (mb_strlen(trim($personSearch)) >= 2)
                        <div class="mt-2 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark">
                            @forelse ($this->peopleResults as $person)
                                @if ($person->student)
                                    <div wire:key="person-linked-{{ $person->id }}" class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 last:border-0 dark:border-unicuces-blue-300/15">
                                        <span class="text-sm text-slate-600 dark:text-slate-300">{{ $person->first_name }} {{ $person->last_name }} — {{ $person->document }}</span>
                                        <flux:badge color="zinc">Ya es estudiante</flux:badge>
                                    </div>
                                @else
                                    <button
                                        wire:key="person-option-{{ $person->id }}"
                                        type="button"
                                        wire:click="selectPerson({{ $person->id }})"
                                        class="flex w-full flex-col items-start gap-1 border-b border-slate-100 px-4 py-3 text-left last:border-0 hover:bg-slate-50 dark:border-unicuces-blue-300/15 dark:hover:bg-unicuces-blue/30"
                                    >
                                        <span class="font-medium text-slate-900 dark:text-white">{{ $person->first_name }} {{ $person->last_name }}</span>
                                        <span class="text-sm text-slate-500 dark:text-slate-300">{{ $person->document }} · {{ $person->email }}</span>
                                    </button>
                                @endif
                            @empty
                                <div class="px-4 py-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-300">No encontramos una persona con esos datos.</p>
                                    <flux:button type="button" class="mt-3" variant="outline" icon="plus" wire:click="openCreatePersonModal">
                                        Crear persona y registrarla como estudiante
                                    </flux:button>
                                </div>
                            @endforelse
                        </div>
                    @endif
                @endif
            </flux:field>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end dark:border-unicuces-blue-300/20">
                <flux:button variant="filled" :href="route('students.index', ['current_team' => $currentTeam])">Cancelar</flux:button>
                <flux:button variant="primary" type="submit" icon="plus" :disabled="! $personId">
                    Registrar estudiante
                </flux:button>
            </div>
        </form>
    </div>

    <flux:modal name="create-student-person" wire:model.self="showCreatePersonModal" focusable class="max-w-2xl">
        <form wire:submit="createPersonAndStudent" class="space-y-6">
            <div>
                <flux:heading size="lg">Crear persona y estudiante</flux:heading>
                <flux:subheading>La persona nueva quedará asociada automáticamente como estudiante.</flux:subheading>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label for="first-name">Nombres</flux:label>
                    <flux:input id="first-name" wire:model="firstName" required autocomplete="given-name" />
                    <flux:error name="firstName" />
                </flux:field>

                <flux:field>
                    <flux:label for="last-name">Apellidos</flux:label>
                    <flux:input id="last-name" wire:model="lastName" required autocomplete="family-name" />
                    <flux:error name="lastName" />
                </flux:field>

                <flux:field>
                    <flux:label for="document-type">Tipo de documento</flux:label>
                    <flux:select id="document-type" wire:model="documentTypeId" required>
                        <flux:select.option value="">Selecciona una opción</flux:select.option>
                        @foreach ($this->documentTypes as $documentType)
                            <flux:select.option value="{{ $documentType->id }}">{{ $documentType->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="documentTypeId" />
                </flux:field>

                <flux:field>
                    <flux:label for="person-document">Número de documento</flux:label>
                    <flux:input id="person-document" wire:model="document" required maxlength="20" autocomplete="off" />
                    <flux:error name="document" />
                </flux:field>

                <flux:field>
                    <flux:label for="person-email">Correo electrónico</flux:label>
                    <flux:input id="person-email" wire:model="email" type="email" required autocomplete="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label for="person-phone">Teléfono</flux:label>
                    <flux:input id="person-phone" wire:model="phone" maxlength="20" autocomplete="tel" />
                    <flux:error name="phone" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-200 pt-5 dark:border-unicuces-blue-300/20">
                <flux:modal.close>
                    <flux:button variant="filled" type="button">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" type="submit" icon="plus">Crear estudiante</flux:button>
            </div>
        </form>
    </flux:modal>
</div>