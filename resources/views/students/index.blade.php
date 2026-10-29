<x-layouts::app :title="__('Estudiantes')">
    <section class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <div class="flex flex-col gap-4 rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-8 dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Estudiantes</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <flux:button variant="primary" icon="plus" :href="route('students.create', ['current_team' => request()->route('current_team')])">
                Agregar estudiante
            </flux:button>
        </div>

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:callout>
        @endif

        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_70px_-45px_rgba(15,23,42,0.45)] dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 sm:py-7 dark:border-unicuces-blue-300/20">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <flux:heading size="lg">Listado de estudiantes</flux:heading>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-300">{{ $students->total() }} registros</p>
                </div>
                <flux:subheading>Código, persona vinculada y estado del registro.</flux:subheading>

                <form method="GET" action="{{ route('students.index', ['current_team' => request()->route('current_team')]) }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <flux:input
                        name="search"
                        type="search"
                        icon="magnifying-glass"
                        placeholder="Buscar por código, nombre, documento o correo"
                        aria-label="Buscar estudiantes"
                        maxlength="255"
                        :value="$search"
                        class="sm:flex-1"
                    />
                    <flux:button type="submit" variant="primary" icon="magnifying-glass">Buscar</flux:button>
                </form>
                <flux:error name="search" />
            </div>

            @if ($students->isEmpty())
                <div class="m-6 rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-8 py-16 text-center sm:m-8 dark:border-unicuces-blue-300/30 dark:bg-unicuces-blue/25">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-3xl bg-unicuces-blue-100 text-unicuces-blue-700 dark:bg-unicuces-blue-300/15 dark:text-unicuces-blue-100">
                        <flux:icon name="academic-cap" class="size-8" />
                    </div>
                    <flux:heading size="lg" class="mt-5">{{ $search === '' ? 'No hay estudiantes registrados' : 'No se encontraron estudiantes' }}</flux:heading>
                    <flux:subheading class="mx-auto mt-2 max-w-xl">{{ $search === '' ? 'Los registros de estudiantes aparecerán aquí cuando se creen en el sistema.' : 'Prueba con otro código, nombre, documento o correo.' }}</flux:subheading>
                </div>
            @else
                <div class="px-4 pb-5 pt-3 sm:px-6 sm:pb-6 sm:pt-4 lg:px-8">
                    <flux:table container:class="overflow-x-auto">
                        <flux:table.columns class="bg-slate-50/90 dark:bg-unicuces-blue/35">
                            <flux:table.column>Código</flux:table.column>
                            <flux:table.column>Estudiante</flux:table.column>
                            <flux:table.column>Documento</flux:table.column>
                            <flux:table.column>Correo</flux:table.column>
                            <flux:table.column>Estado</flux:table.column>
                            <flux:table.column align="end"></flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($students as $student)
                                <flux:table.row :key="$student->id">
                                    <flux:table.cell class="py-5">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $student->code }}</p>
                                    </flux:table.cell>
                                    <flux:table.cell class="py-5">
                                        <p class="font-medium text-slate-800 dark:text-slate-100">{{ $student->person->first_name }} {{ $student->person->last_name }}</p>
                                    </flux:table.cell>
                                    <flux:table.cell class="py-5">
                                        {{ $student->person->document }}
                                    </flux:table.cell>
                                    <flux:table.cell class="py-5">
                                        {{ $student->person->email }}
                                    </flux:table.cell>
                                    <flux:table.cell class="py-5">
                                        <flux:badge :color="$student->is_active ? 'green' : 'zinc'">
                                            {{ $student->is_active ? 'Activo' : 'Inactivo' }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell align="end" class="py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button variant="outline" size="sm" icon="pencil-square" :href="route('students.edit', ['current_team' => request()->route('current_team'), 'students' => $student])">
                                                Editar
                                            </flux:button>
                                            <form method="POST" action="{{ route('students.destroy', ['current_team' => request()->route('current_team'), 'students' => $student]) }}" onsubmit="return confirm('¿Deseas eliminar este estudiante?');">
                                                @csrf
                                                @method('DELETE')

                                                <flux:button variant="danger" size="sm" icon="trash" type="submit">Eliminar</flux:button>
                                            </form>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>

                    @if ($students->hasPages())
                        <div class="border-t border-slate-200 pt-5 dark:border-unicuces-blue-300/20">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</x-layouts::app>
