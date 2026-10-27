<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 antialiased dark:bg-linear-to-b dark:from-unicuces-blue-dark dark:to-unicuces-blue">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-slate-50 p-6 md:p-10 dark:bg-transparent">
            <div class="flex w-full max-w-md flex-col gap-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <img src="{{ asset('images/unicuces-logo.png') }}" alt="UNICUCES" class="h-auto w-56 max-w-full object-contain">
                </a>

                <div class="flex flex-col gap-6">
                    <div class="rounded-xl border border-slate-200 bg-white text-slate-800 shadow-xl shadow-unicuces-blue/10 dark:border-unicuces-gold/20 dark:bg-unicuces-blue dark:text-white">
                        <div class="px-10 py-8">{{ $slot }}</div>
                    </div>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
