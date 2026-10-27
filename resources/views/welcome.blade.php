<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-800 antialiased dark:bg-unicuces-blue-dark dark:text-white">
        <main class="grid min-h-screen lg:grid-cols-2">
            <section class="relative flex min-h-[24rem] flex-col justify-between overflow-hidden bg-linear-to-br from-unicuces-blue via-[#004080] to-unicuces-blue-dark px-8 py-10 text-white sm:px-12 lg:min-h-screen lg:px-16 lg:py-14">
                <div class="absolute -right-28 -top-28 size-96 rounded-full border border-white/10"></div>
                <div class="absolute -bottom-48 -left-24 size-96 rounded-full border border-white/10"></div>

                <a href="{{ route('home') }}" class="relative z-10 flex w-fit items-center rounded-lg bg-white px-3 py-2" wire:navigate>
                    <img src="{{ asset('images/unicuces-logo.png') }}" alt="UNICUCES" class="h-auto w-56 object-contain">
                </a>

                <div class="relative z-10 mt-16 max-w-lg">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white/75">Corporación Universitaria Centro Superior</p>
                    <p class="mt-4 text-3xl font-semibold leading-tight sm:text-4xl">Conocimiento que transforma.</p>
                </div>
            </section>

            <section class="flex items-center justify-center px-6 py-16 sm:px-10 lg:px-16">
                <div class="w-full max-w-lg">
                    <span class="inline-flex items-center rounded-full bg-unicuces-gold-soft px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-unicuces-blue dark:bg-unicuces-gold/15 dark:text-unicuces-gold">
                        Portal institucional
                    </span>

                    <h1 class="mt-6 text-4xl font-semibold tracking-tight text-unicuces-navy dark:text-white sm:text-5xl">
                        Bienvenido a UNICUCES
                    </h1>
                    <p class="mt-5 max-w-md text-lg leading-8 text-zinc-600 dark:text-zinc-300">
                        Inicia sesión para acceder al portal institucional.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:gap-4">
                        <a href="{{ route('login') }}" class="inline-flex min-h-12 w-full items-center justify-center whitespace-nowrap rounded-full bg-unicuces-gold px-6 py-3 text-sm font-semibold text-unicuces-blue-dark transition hover:bg-unicuces-gold-dark sm:w-auto">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex min-h-12 w-full items-center justify-center whitespace-nowrap rounded-full border border-zinc-300 px-6 py-3 text-sm font-semibold text-zinc-700 transition hover:border-zinc-400 hover:text-zinc-900 dark:border-zinc-700 dark:text-zinc-200 dark:hover:text-white sm:w-auto">
                            Registrarse
                        </a>
                    </div>

                    <div class="mt-12 h-1 w-20 rounded-full bg-unicuces-gold"></div>
                </div>
            </section>
        </main>
    </body>
</html>
