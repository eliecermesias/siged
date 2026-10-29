<x-layouts::app :title="__('Agregar coordinador')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('coordinators.index', ['current_team' => request()->route('current_team')])">Coordinadores</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Agregar coordinador</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <livewire:coordinators.create-form :current-team="request()->route('current_team')" />
    </section>
</x-layouts::app>
