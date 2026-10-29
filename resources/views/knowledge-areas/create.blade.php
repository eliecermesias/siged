<x-layouts::app :title="__('Agregar área del conocimiento')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('knowledge-areas.index', ['current_team' => request()->route('current_team')])">Áreas del conocimiento</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Agregar área</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 dark:border-unicuces-blue-300/20">
                <flux:heading size="lg">Agregar área del conocimiento</flux:heading>
                <flux:subheading>Registra un área para clasificar los programas académicos.</flux:subheading>
            </div>
            <form method="POST" action="{{ route('knowledge-areas.store', ['current_team' => request()->route('current_team')]) }}" class="space-y-8 px-6 py-7 sm:px-8">
                @csrf
                @include('knowledge-areas._form', ['knowledgeArea' => null])
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end dark:border-unicuces-blue-300/20">
                    <flux:button variant="filled" :href="route('knowledge-areas.index', ['current_team' => request()->route('current_team')])">Cancelar</flux:button>
                    <flux:button variant="primary" type="submit" icon="check">Guardar área</flux:button>
                </div>
            </form>
        </div>
    </section>
</x-layouts::app>
