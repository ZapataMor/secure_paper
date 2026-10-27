<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
            /* Controles legibles y cómodos al tacto en el espacio de trabajo. */
            @media (max-width: 640px) {
                .sp-admin-actions,
                .sp-admin-user-menu,
                .sp-admin-user-menu > [data-flux-dropdown] {
                    min-width: 0;
                    max-width: 100%;
                }
                .sp-admin-user-menu [data-test="user-menu-button"] {
                    min-width: 0;
                    max-width: 100%;
                    min-height: 44px;
                }
                .sp-admin-user-menu [data-test="user-menu-button"] > span {
                    min-width: 0;
                }
                .sp-admin-user-menu [data-flux-menu] {
                    max-width: calc(100vw - 2rem);
                }
                .sp-admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
                .sp-admin-content select,
                .sp-admin-content textarea {
                    font-size: 1rem !important;
                    max-width: 100%;
                    min-width: 0;
                }
                .sp-admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
                .sp-admin-content select,
                .sp-admin-content button {
                    min-height: 44px;
                }
                .sp-admin-content button {
                    max-width: 100%;
                    white-space: normal;
                    overflow-wrap: anywhere;
                }
            }
            @media (max-width: 480px) {
                .sp-admin-navbar .sp-admin-brand { max-width: 100%; }
                .sp-admin-modules-nav {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: .35rem;
                }
                .sp-admin-modules-nav .sp-admin-module-link {
                    justify-content: center;
                    min-width: 0;
                    min-height: 44px;
                    padding: .4rem .5rem;
                    font-size: .875rem;
                    text-align: center;
                    white-space: normal;
                }
            }
        </style>
    </head>
    <body class="sp-admin-shell">
        @php
            $currentUser = auth()->user();
            $isAdmin = $currentUser?->isAdmin();
            $isAdvisor = $currentUser?->isAdvisor();
            $isClient = $currentUser?->isClient();
            $hasActiveMembership = $isClient && ($currentUser?->hasActiveMembership() ?? false);
        @endphp

        @persist('admin-navbar')
        <header class="sp-admin-navbar">
            <div class="sp-admin-navbar-inner">
                <div class="sp-admin-brand" aria-label="Professional Papers">
                    <span class="sp-admin-brand-mark" aria-hidden="true">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="">
                    </span>
                    <span class="sp-admin-brand-copy">
                        <h1 class="truncate whitespace-nowrap text-2xl font-bold tracking-tight sm:text-3xl">propaps<span class="text-[#2CC295]">.com</span></h1>
                    </span>
                </div>

                <nav class="sp-admin-modules-nav">
                    <a
                        href="{{ route('dashboard') }}"
                        wire:navigate
                        class="sp-admin-module-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                    >
                        Inicio
                    </a>

                    @if($isAdmin)
                        <a href="{{ route('admin.payment-quotes') }}" class="sp-admin-module-link">Cotizaciones</a>
                    @endif
                    @if($isClient)
                        <a
                            href="{{ route('private.planes') }}"
                            wire:navigate
                            class="sp-admin-module-link {{ request()->routeIs('private.planes') ? 'is-active' : '' }}"
                        >
                            Planes
                        </a>

                        <a
                            href="{{ route('private.upload-document') }}"
                            wire:navigate
                            class="sp-admin-module-link {{ request()->routeIs('private.upload-document') ? 'is-active' : '' }} {{ $hasActiveMembership ? '' : 'pointer-events-none opacity-55' }}"
                            aria-disabled="{{ $hasActiveMembership ? 'false' : 'true' }}"
                            title="{{ $hasActiveMembership ? 'Cargar documento' : 'Disponible al activar una membresia pagada' }}"
                        >
                            Cargar documentos
                        </a>
                    @endif

                    @if($isAdmin || $isAdvisor)
                        <a
                            href="{{ route('admin.works.index') }}"
                            wire:navigate
                            class="sp-admin-module-link {{ request()->routeIs('admin.works.*') ? 'is-active' : '' }}"
                        >
                            Proyectos
                        </a>
                    @endif

                    @if($isAdmin)
                        <a
                            href="{{ route('admin.users.index') }}"
                            wire:navigate
                            class="sp-admin-module-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}"
                        >
                            Gestion de usuarios
                        </a>
                    @endif

                    <span class="sp-admin-nav-indicator" aria-hidden="true"></span>
                </nav>

                <div class="sp-admin-actions">
                    <div class="sp-admin-user-menu">
                        <x-desktop-user-menu />
                    </div>
                </div>
            </div>
        </header>
        @endpersist

        <main class="sp-admin-content">
            {{ $slot }}
        </main>

        @vite('resources/js/layouts/app/navbar.js')

        @fluxScripts
    </body>
</html>
