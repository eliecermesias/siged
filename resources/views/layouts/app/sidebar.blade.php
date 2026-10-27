<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-800 dark:bg-unicuces-blue-dark dark:text-slate-100">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-slate-200 bg-white dark:border-unicuces-blue-300/25 dark:bg-linear-to-b dark:from-unicuces-blue dark:to-unicuces-blue-dark">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <livewire:team-switcher />

            @php
                $resolveMenuUrl = static function (string $url): string {
                    if (! str_starts_with($url, 'route:')) {
                        return $url;
                    }

                    $routeName = str($url)->after('route:')->toString();

                    return Route::has($routeName) ? route($routeName) : '#';
                };
            @endphp

            <flux:sidebar.nav>
                @foreach ($menu as $item)
                    @if ($item->children->isEmpty())
                        <flux:sidebar.item
                            :icon="$item->icon"
                            :href="$resolveMenuUrl($item->url)"
                            :current="request()->routeIs($item->current)"
                            wire:navigate
                        >
                            {{ $item->name }}
                        </flux:sidebar.item>
                    @else
                        <flux:sidebar.group
                            expandable
                            :expanded="request()->routeIs($item->current) || $item->children->contains(fn ($child) => request()->routeIs($child->current))"
                            :heading="$item->name"
                            :icon="$item->icon"
                            class="grid"
                        >
                            @foreach ($item->children as $child)
                                <flux:sidebar.item
                                    :icon="$child->icon"
                                    :href="$resolveMenuUrl($child->url)"
                                    :current="request()->routeIs($child->current)"
                                    wire:navigate
                                >
                                    {{ $child->name }}
                                </flux:sidebar.item>
                            @endforeach
                        </flux:sidebar.group>
                    @endif
                @endforeach
            </flux:sidebar.nav>

            <flux:spacer />

            

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="border-b border-slate-200 bg-white dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    @if (Route::has('logout'))
                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer"
                                data-test="logout-button"
                            >
                                {{ __('Log out') }}
                            </flux:menu.item>
                        </form>
                    @endif
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <livewire:create-team-modal />

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
