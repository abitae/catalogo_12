<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                <flux:navlist.group :expanded="request()->routeIs('catalogo.*')" expandable heading="Catalogo">
                    <flux:navlist.item icon="cube" :href="route('catalogo.products')" :current="request()->routeIs('catalogo.products')" wire:navigate>{{ __('Productos') }}</flux:navlist.item>
                    <flux:navlist.item icon="tag" :href="route('catalogo.brands')" :current="request()->routeIs('catalogo.brands')" wire:navigate>{{ __('Marcas') }}</flux:navlist.item>
                    <flux:navlist.item icon="squares-2x2" :href="route('catalogo.categories')" :current="request()->routeIs('catalogo.categories')" wire:navigate>{{ __('Categorías') }}</flux:navlist.item>
                    <flux:navlist.item icon="bars-3" :href="route('catalogo.lines')" :current="request()->routeIs('catalogo.lines')" wire:navigate>{{ __('Líneas') }}</flux:navlist.item>
                </flux:navlist.group>
                <flux:navlist.group :expanded="request()->routeIs('almacen.*')" expandable heading="Almacén">
                    <flux:navlist.item icon="building-storefront" :href="route('almacen.warehouses')" :current="request()->routeIs('almacen.warehouses')" wire:navigate>{{ __('Almacenes') }}</flux:navlist.item>
                    <flux:navlist.item icon="cube-transparent" :href="route('almacen.products')" :current="request()->routeIs('almacen.products')" wire:navigate>{{ __('Productos') }}</flux:navlist.item>
                    <flux:navlist.item icon="arrow-path" :href="route('almacen.transfers')" :current="request()->routeIs('almacen.transfers')" wire:navigate>{{ __('Transferencias') }}</flux:navlist.item>
                    <flux:navlist.item icon="arrows-right-left" :href="route('almacen.movements')" :current="request()->routeIs('almacen.movements')" wire:navigate>{{ __('Movimientos') }}</flux:navlist.item>
                </flux:navlist.group>
                <flux:navlist.group :expanded="request()->routeIs('crm.*')" expandable heading="CRM">
                    <flux:navlist.item icon="chart-bar" :href="route('crm.opportunities')" :current="request()->routeIs('crm.opportunities')" wire:navigate>{{ __('Oportunidades') }}</flux:navlist.item>
                    <flux:navlist.item icon="user-group" :href="route('crm.contacts')" :current="request()->routeIs('crm.contacts')" wire:navigate>{{ __('Contactos') }}</flux:navlist.item>
                    <flux:navlist.item icon="calendar-days" :href="route('crm.activities')" :current="request()->routeIs('crm.activities')" wire:navigate>{{ __('Actividades') }}</flux:navlist.item>
                    <flux:navlist.item icon="tag" :href="route('crm.marcas')" :current="request()->routeIs('crm.marcas')" wire:navigate>{{ __('Marcas') }}</flux:navlist.item>
                    <flux:navlist.item icon="building-office" :href="route('crm.tipos-negocio')" :current="request()->routeIs('crm.tipos-negocio')" wire:navigate>{{ __('Tipos de Negocio') }}</flux:navlist.item>
                </flux:navlist.group>
                <flux:navlist.group :expanded="request()->routeIs('almacen.reportes.*')" expandable heading="Reportes">
                    <flux:navlist.item icon="document-chart-bar" :href="route('almacen.reportes.lotes')" :current="request()->routeIs('almacen.reportes.lotes')" wire:navigate>{{ __('Reportes') }}</flux:navlist.item>
                    <flux:navlist.item icon="exclamation-triangle" :href="route('almacen.alertas.lotes')" :current="request()->routeIs('almacen.alertas.lotes')" wire:navigate>{{ __('Alertas') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
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
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
