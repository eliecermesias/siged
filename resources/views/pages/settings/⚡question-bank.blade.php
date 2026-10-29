<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Question bank settings')] class extends Component {};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Question bank settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Question bank')" :subheading="__('Manage evaluation intentions, modalities and instruments')">
        <flux:card class="w-full">
            <flux:card.header>
                <flux:card.heading>Configuración del banco</flux:card.heading>
                <flux:card.subheading>Define las intenciones, modalidades, instrumentos, versiones y características de las evaluaciones.</flux:card.subheading>
            </flux:card.header>
            <flux:card.body>
                <flux:text>Selecciona una sección en el menú lateral para administrar la configuración.</flux:text>
            </flux:card.body>
        </flux:card>
    </x-pages::settings.layout>
</section>