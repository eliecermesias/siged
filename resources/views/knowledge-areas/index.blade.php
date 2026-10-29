<x-layouts::app :title="__('Áreas del conocimiento')">
    <section class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <div class="flex flex-col gap-4 rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-8 dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Áreas del conocimiento</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <flux:button variant="primary" icon="plus" :href="route('knowledge-areas.create', ['current_team' => request()->route('current_team')])">Agregar área</flux:button>
        </div>

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
        @endif
        @if (session('error'))
            <flux:callout variant="danger" icon="x-circle">{{ session('error') }}</flux:callout>
        @endif

        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 dark:border-unicuces-blue-300/20">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <flux:heading size="lg">Listado de áreas del conocimiento</flux:heading>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-300">{{ $knowledgeAreas->total() }} registros</p>
                </div>
                <flux:subheading>Administra las áreas disponibles para los programas académicos.</flux:subheading>
                <form method="GET" action="{{ route('knowledge-areas.index', ['current_team' => request()->route('current_team')]) }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <flux:input name="search" type="search" icon="magnifying-glass" placeholder="Buscar por nombre" aria-label="Buscar áreas del conocimiento" maxlength="255" :value="$search" class="sm:flex-1" />
                    <flux:button type="submit" variant="primary" icon="magnifying-glass">Buscar</flux:button>
                </form>
                <flux:error name="search" />
            </div>

            @if ($knowledgeAreas->isEmpty())
                <div class="px-8 py-16 text-center">
                    <flux:heading size="lg">{{ $search === '' ? 'No hay áreas registradas' : 'No se encontraron áreas' }}</flux:heading>
                    <flux:subheading class="mt-2">{{ $search === '' ? 'Agrega un área para comenzar.' : 'Prueba con otro nombre.' }}</flux:subheading>
                </div>
            @else
                <div class="px-4 pb-5 pt-3 sm:px-6 lg:px-8">
                    <flux:table container:class="overflow-x-auto">
                        <flux:table.columns class="bg-slate-50/90 dark:bg-unicuces-blue/35">
                            <flux:table.column>Nombre</flux:table.column>
                            <flux:table.column>Descripción</flux:table.column>
                            <flux:table.column>Estado</flux:table.column>
                            <flux:table.column align="end"></flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($knowledgeAreas as $knowledgeArea)
                                <flux:table.row :key="$knowledgeArea->id">
                                    <flux:table.cell class="font-medium">{{ $knowledgeArea->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $knowledgeArea->description ?: '—' }}</flux:table.cell>
                                    <flux:table.cell><flux:badge :color="$knowledgeArea->is_active ? 'green' : 'zinc'">{{ $knowledgeArea->is_active ? 'Activo' : 'Inactivo' }}</flux:badge></flux:table.cell>
                                    <flux:table.cell align="end">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button variant="outline" size="sm" icon="pencil-square" :href="route('knowledge-areas.edit', ['current_team' => request()->route('current_team'), 'knowledgeArea' => $knowledgeArea])">Editar</flux:button>
                                            <form method="POST" action="{{ route('knowledge-areas.destroy', ['current_team' => request()->route('current_team'), 'knowledgeArea' => $knowledgeArea]) }}" onsubmit="return confirm('¿Deseas eliminar esta área?');">
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
                    @if ($knowledgeAreas->hasPages())
                        <div class="border-t border-slate-200 pt-5 dark:border-unicuces-blue-300/20">{{ $knowledgeAreas->links() }}</div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</x-layouts::app>
