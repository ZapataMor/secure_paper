<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2 4 6v6c0 5 3.2 9.4 8 10 4.8-.6 8-5 8-10V6z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </span>
                    <span class="sp-admin-brand-copy">
                        <span class="sp-admin-brand-title">Propaps.com</span>
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
                            Trabajos
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
