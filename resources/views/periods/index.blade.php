<x-layouts::app :title="__('Períodos')">
    <section class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <div class="flex flex-col gap-4 rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-8 dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Períodos</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <flux:button variant="primary" icon="plus" :href="route('periods.create', ['current_team' => request()->route('current_team')])">
                Agregar período
            </flux:button>
        </div>

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
        @endif

        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 dark:border-unicuces-blue-300/20">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <flux:heading size="lg">Listado de períodos</flux:heading>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-300">{{ $periods->total() }} registros</p>
                </div>
                <flux:subheading>Períodos académicos ordenados del más nuevo al más antiguo.</flux:subheading>
                <form method="GET" action="{{ route('periods.index', ['current_team' => request()->route('current_team')]) }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <flux:input
                        name="search"
                        type="search"
                        icon="magnifying-glass"
                        placeholder="Buscar por año, período o fecha"
                        aria-label="Buscar períodos"
                        maxlength="255"
                        :value="$search"
                        class="sm:flex-1"
                    />
                    <flux:button type="submit" variant="primary" icon="magnifying-glass">Buscar</flux:button>
                </form>
                <flux:error name="search" />
            </div>

            @if ($periods->isEmpty())
                <div class="px-8 py-16 text-center">
                    <flux:heading size="lg">{{ $search === '' ? 'No hay períodos registrados' : 'No se encontraron períodos' }}</flux:heading>
                    <flux:subheading class="mt-2">{{ $search === '' ? 'Agrega un período para comenzar.' : 'Prueba con otro año, período o fecha.' }}</flux:subheading>
                </div>
            @else
                <div class="px-4 pb-5 pt-3 sm:px-6 lg:px-8">
                    <flux:table container:class="overflow-x-auto">
                        <flux:table.columns class="bg-slate-50/90 dark:bg-unicuces-blue/35">
                            <flux:table.column>Año</flux:table.column>
                            <flux:table.column>Período</flux:table.column>
                            <flux:table.column>Fecha de inicio</flux:table.column>
                            <flux:table.column>Fecha final</flux:table.column>
                            <flux:table.column>Estado</flux:table.column>
                            <flux:table.column align="end"></flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($periods as $period)
                                <flux:table.row :key="$period->id">
                                    <flux:table.cell class="font-medium">{{ $period->year }}</flux:table.cell>
                                    <flux:table.cell class="font-semibold">{{ $period->period }}</flux:table.cell>
                                    <flux:table.cell>{{ $period->start_date?->format('d/m/Y') ?? 'Sin definir' }}</flux:table.cell>
                                    <flux:table.cell>{{ $period->end_date?->format('d/m/Y') ?? 'Sin definir' }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge :color="$period->is_active ? 'green' : 'zinc'">{{ $period->is_active ? 'Activo' : 'Inactivo' }}</flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell align="end">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button variant="outline" size="sm" icon="pencil-square" :href="route('periods.edit', ['current_team' => request()->route('current_team'), 'periods' => $period])">
                                                Editar
                                            </flux:button>
                                            <form method="POST" action="{{ route('periods.destroy', ['current_team' => request()->route('current_team'), 'periods' => $period]) }}" onsubmit="return confirm('¿Deseas eliminar este período?');">
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
                    @if ($periods->hasPages())
                        <div class="border-t border-slate-200 pt-5 dark:border-unicuces-blue-300/20">
                            {{ $periods->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</x-layouts::app>
