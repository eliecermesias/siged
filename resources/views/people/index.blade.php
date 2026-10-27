<x-layouts::app :title="__('Personas')">
    <section class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <div class="flex flex-col gap-4 rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-8 dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Personas</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <flux:button variant="primary" icon="plus" :href="route('people.create', ['current_team' => request()->route('current_team')])">
                Agregar persona
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
                    <flux:heading size="lg">Listado de personas</flux:heading>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-300">{{ $people->total() }} registros</p>
                </div>
                <flux:subheading>Nombre, identificación y datos de contacto registrados.</flux:subheading>
            </div>

            @if ($people->isEmpty())
                <div class="m-6 rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-8 py-16 text-center sm:m-8 dark:border-unicuces-blue-300/30 dark:bg-unicuces-blue/25">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-3xl bg-unicuces-blue-100 text-unicuces-blue-700 dark:bg-unicuces-blue-300/15 dark:text-unicuces-blue-100">
                        <flux:icon name="users" class="size-8" />
                    </div>
                    <flux:heading size="lg" class="mt-5">No hay personas registradas</flux:heading>
                    <flux:subheading class="mx-auto mt-2 max-w-xl">Los registros de personas aparecerán aquí cuando se creen en el sistema.</flux:subheading>
                </div>
            @else
                <div class="px-4 pb-5 pt-3 sm:px-6 sm:pb-6 sm:pt-4 lg:px-8">
                    <flux:table :paginate="$people" container:class="overflow-x-auto">
                        <flux:table.columns class="bg-slate-50/90 dark:bg-unicuces-blue/35">
                            <flux:table.column>Persona</flux:table.column>
                            <flux:table.column>Documento</flux:table.column>
                            <flux:table.column>Contacto</flux:table.column>
                            
                            <flux:table.column align="end"></flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($people as $person)
                                <flux:table.row :key="$person->id">
                                    <flux:table.cell class="py-5">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $person->first_name }} {{ $person->last_name }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Registro #{{ $person->id }}</p>
                                    </flux:table.cell>
                                    <flux:table.cell class="py-5">
                                        <p class="font-medium text-slate-800 dark:text-slate-100">{{ $person->documentType?->name ?? 'Sin tipo' }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">{{ $person->document }}</p>
                                    </flux:table.cell>
                                    <flux:table.cell class="py-5">
                                        <p class="font-medium text-slate-800 dark:text-slate-100">{{ $person->email }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">{{ $person->phone ?: 'Sin teléfono' }}</p>
                                    </flux:table.cell>
                                    
                                    <flux:table.cell align="end" class="py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button variant="outline" size="sm" icon="pencil-square" :href="route('people.edit', ['current_team' => request()->route('current_team'), 'people' => $person])">
                                                Editar
                                            </flux:button>
                                            <form method="POST" action="{{ route('people.destroy', ['current_team' => request()->route('current_team'), 'people' => $person]) }}" onsubmit="return confirm('¿Deseas eliminar a esta persona?');">
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
                </div>
            @endif
        </div>
    </section>
</x-layouts::app>
