@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="UNICUCES" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-9 items-center justify-center rounded-md border border-zinc-200 bg-white p-0.5 dark:border-white/10">
            <x-app-logo-icon class="size-full" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="UNICUCES" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-9 items-center justify-center rounded-md border border-zinc-200 bg-white p-0.5 dark:border-white/10">
            <x-app-logo-icon class="size-full" />
        </x-slot>
    </flux:brand>
@endif
