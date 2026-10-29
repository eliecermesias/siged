<x-layouts::app :title="__('Editar docente')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('teachers.index', ['current_team' => request()->route('current_team')])">Docentes</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Editar docente</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_70px_-45px_rgba(15,23,42,0.45)] dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 sm:py-7 dark:border-unicuces-blue-300/20">
                <flux:heading size="lg">Editar docente</flux:heading>
                <flux:subheading>Actualiza el registro {{ $teacher->code }}.</flux:subheading>
            </div>

            <form method="POST" action="{{ route('teachers.update', ['current_team' => request()->route('current_team'), 'teachers' => $teacher]) }}" class="space-y-8 px-6 py-7 sm:px-8 sm:py-8">
                @csrf
                @method('PUT')

                @include('teachers._form', ['teacher' => $teacher, 'people' => $people])

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end dark:border-unicuces-blue-300/20">
                    <flux:button variant="filled" :href="route('teachers.index', ['current_team' => request()->route('current_team')])">Cancelar</flux:button>
                    <flux:button variant="primary" type="submit" icon="check">Guardar cambios</flux:button>
                </div>
            </form>
        </div>
    </section>
</x-layouts::app>
