<x-layouts::app :title="$resourceTitle">
    <section @class([
        'w-full' => $indexRoute !== 'questions.index',
        'flex w-full flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10' => $indexRoute === 'questions.index',
    ])>
        @if ($indexRoute !== 'questions.index')
            @include('partials.settings-heading')
        @endif

        <div class="flex items-start max-md:flex-col">
            @if ($indexRoute !== 'questions.index')
                <aside class="me-10 w-full pb-4 md:w-[220px]">
                    @include('partials.settings.question-bank-navigation')
                </aside>
            @endif

            <div class="flex-1 self-stretch max-md:pt-6">
                @if (session('success'))
                    <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
                @endif
                @if (session('error'))
                    <flux:callout variant="danger" icon="x-circle">{{ session('error') }}</flux:callout>
                @endif

                <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
                    <div class="border-b border-slate-200 px-6 py-6 sm:px-8 dark:border-unicuces-blue-300/20">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex flex-col gap-1">
                                <flux:heading size="lg">Listado: {{ $resourceTitle }}</flux:heading>
                                <flux:subheading>{{ $resourceDescription }}</flux:subheading>
                            </div>
                            <div class="flex shrink-0 items-center justify-between gap-4 sm:justify-end">
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-300">{{ $records->total() }} registros</p>
                                <flux:button variant="primary" icon="plus" :href="route($createRoute, ['current_team' => request()->route('current_team')])">
                                    Agregar registro
                                </flux:button>
                            </div>
                        </div>
                        <form id="question-bank-search" method="GET" action="{{ route($indexRoute, ['current_team' => request()->route('current_team')]) }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                            <flux:input
                                name="search"
                                type="search"
                                icon="magnifying-glass"
                                placeholder="Buscar"
                                aria-label="Buscar {{ Str::lower($resourceTitle) }}"
                                maxlength="255"
                                :value="$search"
                                class="sm:flex-1"
                            />
                            <flux:button type="submit" variant="primary" icon="magnifying-glass">Buscar</flux:button>
                        </form>
                        <flux:error name="search" />
                    </div>

                    @if ($records->isEmpty())
                        <div class="px-8 py-16 text-center">
                            <flux:heading size="lg">{{ $search === '' ? 'No hay registros' : 'No se encontraron registros' }}</flux:heading>
                            <flux:subheading class="mt-2">{{ $search === '' ? 'Agrega un registro para comenzar.' : 'Prueba con otros términos de búsqueda.' }}</flux:subheading>
                        </div>
                    @else
                        <div class="px-4 pb-5 pt-3 sm:px-6 lg:px-8">
                            <flux:table container:class="overflow-x-auto">
                                <flux:table.columns class="bg-slate-50/90 dark:bg-unicuces-blue/35">
                                    @foreach ($columns as $column)
                                        <flux:table.column>{{ $column['label'] }}</flux:table.column>
                                    @endforeach
                                    <flux:table.column align="end"></flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @foreach ($records as $record)
                                        <flux:table.row :key="$record->id">
                                            @foreach ($columns as $column)
                                                <flux:table.cell>
                                                    @if (($column['type'] ?? null) === 'status')
                                                        <flux:badge :color="data_get($record, $column['field']) ? 'green' : 'zinc'">
                                                            {{ data_get($record, $column['field']) ? 'Activo' : 'Inactivo' }}
                                                        </flux:badge>
                                                    @elseif (($column['type'] ?? null) === 'boolean')
                                                        {{ data_get($record, $column['field']) ? 'Sí' : 'No' }}
                                                    @else
                                                        {{ data_get($record, $column['field']) ?? '—' }}
                                                    @endif
                                                </flux:table.cell>
                                            @endforeach
                                            <flux:table.cell align="end">
                                                <div class="flex items-center justify-end gap-2">
                                                    <flux:button variant="outline" size="sm" icon="pencil-square" :href="route($editRoute, ['current_team' => request()->route('current_team'), $routeParameter => $record])">
                                                        Editar
                                                    </flux:button>
                                                    <form method="POST" action="{{ route($deleteRoute, ['current_team' => request()->route('current_team'), $routeParameter => $record]) }}" onsubmit="return confirm('¿Deseas eliminar este registro?');">
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
                            @if ($records->hasPages())
                                <div class="border-t border-slate-200 pt-5 dark:border-unicuces-blue-300/20">{{ $records->links() }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-layouts::app>
