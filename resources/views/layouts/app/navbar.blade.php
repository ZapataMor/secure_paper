<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="sp-admin-shell">
        @php
            $currentUser = auth()->user();
            $isAdmin = $currentUser?->isAdmin();
        @endphp

        <header class="sp-admin-navbar">
            <div class="sp-admin-navbar-inner">
                <a href="{{ route('dashboard') }}" class="sp-admin-brand" wire:navigate>
                    <span class="sp-admin-brand-mark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2 4 6v6c0 5 3.2 9.4 8 10 4.8-.6 8-5 8-10V6z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </span>
                    <span class="sp-admin-brand-text">
                        Secure Papers
                    </span>
                </a>

                <nav class="sp-admin-modules-nav">
                    <a
                        href="{{ route('dashboard') }}"
                        wire:navigate
                        class="sp-admin-module-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                    >
                        Inicio
                    </a>

                    @if($isAdmin)
                        <a
                            href="{{ route('admin.users.index') }}"
                            wire:navigate
                            class="sp-admin-module-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}"
                        >
                            Gestion de usuarios
                        </a>
                    @endif
                </nav>

                <div class="sp-admin-actions">
                    <button
                        type="button"
                        class="sp-admin-menu-toggle"
                        id="spModulesToggle"
                        aria-controls="spModulesPanel"
                        aria-expanded="false"
                        aria-label="Abrir modulos"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18M3 12h18M3 18h18" />
                        </svg>
                    </button>

                    <div class="sp-admin-user-menu">
                        <x-desktop-user-menu />
                    </div>
                </div>
            </div>

            <div class="sp-admin-mobile-panel" id="spModulesPanel">
                <a href="{{ route('dashboard') }}" class="sp-admin-mobile-link" wire:navigate>Inicio</a>

                @if($isAdmin)
                    <a href="{{ route('admin.users.index') }}" class="sp-admin-mobile-link" wire:navigate>
                        Gestion de usuarios
                    </a>
                @endif
            </div>
        </header>

        <main class="sp-admin-content">
            {{ $slot }}
        </main>

        <script>
            (() => {
                const bindModulesDropdown = () => {
                    const toggle = document.getElementById('spModulesToggle');
                    const panel = document.getElementById('spModulesPanel');

                    if (!toggle || !panel || toggle.dataset.bound === 'true') {
                        return;
                    }

                    toggle.dataset.bound = 'true';

                    toggle.addEventListener('click', () => {
                        const isOpen = panel.classList.toggle('is-open');
                        toggle.setAttribute('aria-expanded', String(isOpen));
                    });

                    panel.querySelectorAll('a').forEach((link) => {
                        link.addEventListener('click', () => {
                            panel.classList.remove('is-open');
                            toggle.setAttribute('aria-expanded', 'false');
                        });
                    });
                };

                bindModulesDropdown();
                document.addEventListener('livewire:navigated', bindModulesDropdown);
            })();
        </script>

        @fluxScripts
    </body>
</html>
