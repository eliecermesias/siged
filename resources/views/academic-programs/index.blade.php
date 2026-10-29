<x-layouts::app :title="__('Programas académicos')">
    <section class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <div class="flex flex-col gap-4 rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-8 dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Programas académicos</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <flux:button variant="primary" icon="plus" :href="route('academic-programs.create', ['current_team' => request()->route('current_team')])">Agregar programa</flux:button>
        </div>

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
        @endif

        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 dark:border-unicuces-blue-300/20">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <flux:heading size="lg">Listado de programas académicos</flux:heading>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-300">{{ $academicPrograms->total() }} registros</p>
                </div>
                <flux:subheading>Programa, código SNIES, nivel de formación y área del conocimiento.</flux:subheading>
                <form method="GET" action="{{ route('academic-programs.index', ['current_team' => request()->route('current_team')]) }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <flux:input name="search" type="search" icon="magnifying-glass" placeholder="Buscar por programa, SNIES, nivel o área" aria-label="Buscar programas académicos" maxlength="255" :value="$search" class="sm:flex-1" />
                    <flux:button type="submit" variant="primary" icon="magnifying-glass">Buscar</flux:button>
                </form>
                <flux:error name="search" />
            </div>

            @if ($academicPrograms->isEmpty())
                <div class="px-8 py-16 text-center">
                    <flux:heading size="lg">{{ $search === '' ? 'No hay programas registrados' : 'No se encontraron programas' }}</flux:heading>
                    <flux:subheading class="mt-2">{{ $search === '' ? 'Agrega un programa para comenzar.' : 'Prueba con otro nombre, código, nivel o área.' }}</flux:subheading>
                </div>
            @else
                <div class="px-4 pb-5 pt-3 sm:px-6 lg:px-8">
                    <flux:table container:class="overflow-x-auto">
                        <flux:table.columns class="bg-slate-50/90 dark:bg-unicuces-blue/35">
                            <flux:table.column>Programa</flux:table.column>
                            <flux:table.column>SNIES</flux:table.column>
                            <flux:table.column>Nivel</flux:table.column>
                            <flux:table.column>Área</flux:table.column>
                            <flux:table.column>Estado</flux:table.column>
                            <flux:table.column align="end"></flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($academicPrograms as $academicProgram)
                                <flux:table.row :key="$academicProgram->id">
                                    <flux:table.cell class="font-medium">{{ $academicProgram->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $academicProgram->snies }}</flux:table.cell>
                                    <flux:table.cell>{{ $academicProgram->educationLevel->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $academicProgram->knowledgeArea->name }}</flux:table.cell>
                                    <flux:table.cell><flux:badge :color="$academicProgram->is_active ? 'green' : 'zinc'">{{ $academicProgram->is_active ? 'Activo' : 'Inactivo' }}</flux:badge></flux:table.cell>
                                    <flux:table.cell align="end">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button variant="outline" size="sm" icon="pencil-square" :href="route('academic-programs.edit', ['current_team' => request()->route('current_team'), 'academicPrograms' => $academicProgram])">Editar</flux:button>
                                            <form method="POST" action="{{ route('academic-programs.destroy', ['current_team' => request()->route('current_team'), 'academicPrograms' => $academicProgram]) }}" onsubmit="return confirm('¿Deseas eliminar este programa académico?');">
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
                    @if ($academicPrograms->hasPages())
                        <div class="border-t border-slate-200 pt-5 dark:border-unicuces-blue-300/20">{{ $academicPrograms->links() }}</div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</x-layouts::app>
