<x-layouts::app :title="__('Agregar programa académico')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('academic-programs.index', ['current_team' => request()->route('current_team')])">Programas académicos</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Agregar programa</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 dark:border-unicuces-blue-300/20">
                <flux:heading size="lg">Agregar programa académico</flux:heading>
                <flux:subheading>Registra sus datos y selecciona un nivel y un área existentes.</flux:subheading>
            </div>
            <form method="POST" action="{{ route('academic-programs.store', ['current_team' => request()->route('current_team')]) }}" class="space-y-8 px-6 py-7 sm:px-8">
                @csrf
                @include('academic-programs._form', ['academicProgram' => null])
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end dark:border-unicuces-blue-300/20">
                    <flux:button variant="filled" :href="route('academic-programs.index', ['current_team' => request()->route('current_team')])">Cancelar</flux:button>
                    <flux:button variant="primary" type="submit" icon="check">Guardar programa</flux:button>
                </div>
            </form>
        </div>
    </section>
</x-layouts::app>
