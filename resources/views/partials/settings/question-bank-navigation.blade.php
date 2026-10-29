<flux:navlist aria-label="{{ __('Settings') }}">
    <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
    <flux:navlist.item :href="route('security.edit')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
    <flux:navlist.item :href="route('teams.index')" :current="request()->routeIs('teams.*')" wire:navigate>{{ __('Teams') }}</flux:navlist.item>
    <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>

    @if (auth()->user()->currentTeam)
        @php($currentTeamSlug = auth()->user()->currentTeam->slug)

        <flux:navlist.group heading="Banco de preguntas" class="mt-4">
            <flux:navlist.item
                :href="route('settings.question-bank', ['current_team' => $currentTeamSlug])"
                :current="request()->routeIs('settings.question-bank')"
                icon="queue-list"
                wire:navigate
            >
                Resumen
            </flux:navlist.item>
            <flux:navlist.item
                :href="route('intentions.index', ['current_team' => $currentTeamSlug])"
                :current="request()->routeIs('intentions.*')"
                icon="light-bulb"
                wire:navigate
            >
                Intenciones
            </flux:navlist.item>
            <flux:navlist.item
                :href="route('evaluation-modalities.index', ['current_team' => $currentTeamSlug])"
                :current="request()->routeIs('evaluation-modalities.*')"
                icon="arrows-right-left"
                wire:navigate
            >
                Modalidades
            </flux:navlist.item>
            <flux:navlist.item
                :href="route('evaluation-instruments.index', ['current_team' => $currentTeamSlug])"
                :current="request()->routeIs('evaluation-instruments.*')"
                icon="clipboard-document-list"
                wire:navigate
            >
                Instrumentos
            </flux:navlist.item>
            <flux:navlist.item
                :href="route('instrument-versions.index', ['current_team' => $currentTeamSlug])"
                :current="request()->routeIs('instrument-versions.*')"
                icon="tag"
                wire:navigate
            >
                Versiones
            </flux:navlist.item>
            <flux:navlist.item
                :href="route('characteristics.index', ['current_team' => $currentTeamSlug])"
                :current="request()->routeIs('characteristics.*')"
                icon="squares-2x2"
                wire:navigate
            >
                Características
            </flux:navlist.item>
        </flux:navlist.group>
    @endif
</flux:navlist>
