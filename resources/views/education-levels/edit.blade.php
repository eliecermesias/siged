<x-layouts::app :title="__('Editar nivel de formación')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('education-levels.index', ['current_team' => request()->route('current_team')])">Niveles de formación</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Editar nivel</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 dark:border-unicuces-blue-300/20">
                <flux:heading size="lg">Editar nivel de formación</flux:heading>
                <flux:subheading>Actualiza el nivel {{ $educationLevel->name }}.</flux:subheading>
            </div>
            <form method="POST" action="{{ route('education-levels.update', ['current_team' => request()->route('current_team'), 'educationLevel' => $educationLevel]) }}" class="space-y-8 px-6 py-7 sm:px-8">
                @csrf
                @method('PUT')
                @include('education-levels._form', ['educationLevel' => $educationLevel])
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end dark:border-unicuces-blue-300/20">
                    <flux:button variant="filled" :href="route('education-levels.index', ['current_team' => request()->route('current_team')])">Cancelar</flux:button>
                    <flux:button variant="primary" type="submit" icon="check">Guardar cambios</flux:button>
                </div>
            </form>
        </div>
    </section>
</x-layouts::app>
