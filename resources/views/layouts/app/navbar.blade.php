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

        @persist('admin-navbar')
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
                        Professional Papers
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

                    <a
                        href="{{ route('private.planes') }}"
                        wire:navigate
                        class="sp-admin-module-link {{ request()->routeIs('private.planes') ? 'is-active' : '' }}"
                    >
                        Planes
                    </a>

                    @unless($isAdmin)
                        <a
                            href="{{ route('private.upload-document') }}"
                            wire:navigate
                            class="sp-admin-module-link {{ request()->routeIs('private.upload-document') ? 'is-active' : '' }}"
                        >
                            Cargar documento
                        </a>
                    @endunless

                    @if($isAdmin)
                        <a
                            href="{{ route('admin.works.index') }}"
                            wire:navigate
                            class="sp-admin-module-link {{ request()->routeIs('admin.works.*') ? 'is-active' : '' }}"
                        >
                            Trabajos
                        </a>

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
                <a href="{{ route('private.planes') }}" class="sp-admin-mobile-link" wire:navigate>Planes</a>
                @unless($isAdmin)
                    <a href="{{ route('private.upload-document') }}" class="sp-admin-mobile-link" wire:navigate>Cargar documento</a>
                @endunless

                @if($isAdmin)
                    <a href="{{ route('admin.works.index') }}" class="sp-admin-mobile-link" wire:navigate>
                        Trabajos
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sp-admin-mobile-link" wire:navigate>
                        Gestion de usuarios
                    </a>
                @endif
            </div>
        </header>
        @endpersist

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

                const bindNavIndicator = () => {
                    const nav = document.querySelector('.sp-admin-modules-nav');

                    if (!nav) {
                        return;
                    }

                    const links = Array.from(nav.querySelectorAll('.sp-admin-module-link'))
                        .filter((item) => item.offsetParent !== null);
                    const indicator = nav.querySelector('.sp-admin-nav-indicator');

                    if (!indicator || links.length === 0) {
                        return;
                    }

                    nav.classList.add('has-indicator');

                    const normalizePath = (value) => {
                        try {
                            const url = new URL(value, window.location.origin);
                            const normalized = url.pathname.replace(/\/+$/, '');
                            return normalized === '' ? '/' : normalized;
                        } catch (_) {
                            return '/';
                        }
                    };

                    const syncActiveLinks = (forcedLink = null) => {
                        const currentPath = normalizePath(window.location.href);

                        let activeLink = forcedLink;

                        if (!activeLink) {
                            let bestMatch = null;
                            let bestLength = -1;

                            links.forEach((link) => {
                                const path = normalizePath(link.href);
                                const isExact = currentPath === path;
                                const isNested = path !== '/' && currentPath.startsWith(`${path}/`);

                                if ((isExact || isNested) && path.length > bestLength) {
                                    bestMatch = link;
                                    bestLength = path.length;
                                }
                            });

                            activeLink = bestMatch || links[0];
                        }

                        links.forEach((link) => {
                            link.classList.remove('is-active');
                            link.removeAttribute('aria-current');
                        });

                        activeLink.classList.add('is-active');
                        activeLink.setAttribute('aria-current', 'page');

                        const mobileLinks = Array.from(document.querySelectorAll('.sp-admin-mobile-panel .sp-admin-mobile-link'));

                        mobileLinks.forEach((link) => {
                            link.classList.remove('is-active');
                            const current = normalizePath(link.href) === normalizePath(activeLink.href);

                            if (current) {
                                link.classList.add('is-active');
                            }
                        });

                        return activeLink;
                    };

                    const getActiveLink = () => {
                        return syncActiveLinks();
                    };

                    const moveIndicator = (target) => {
                        if (!target) {
                            return;
                        }

                        const navRect = nav.getBoundingClientRect();
                        const targetRect = target.getBoundingClientRect();
                        const left = targetRect.left - navRect.left;
                        const width = targetRect.width;

                        indicator.style.width = `${width}px`;
                        indicator.style.transform = `translate3d(${left}px, -50%, 0)`;
                        indicator.style.opacity = '1';
                    };

                    moveIndicator(getActiveLink());

                    links.forEach((link) => {
                        if (link.dataset.indicatorBound === 'true') {
                            return;
                        }

                        link.dataset.indicatorBound = 'true';
                        link.addEventListener('click', () => {
                            syncActiveLinks(link);
                            moveIndicator(link);
                        });
                    });

                    if (window.__spIndicatorResizeBound !== true) {
                        window.__spIndicatorResizeBound = true;
                        window.addEventListener('resize', () => {
                            const currentNav = document.querySelector('.sp-admin-modules-nav');
                            const currentIndicator = currentNav?.querySelector('.sp-admin-nav-indicator');
                            const currentLinks = currentNav
                                ? Array.from(currentNav.querySelectorAll('.sp-admin-module-link')).filter((item) => item.offsetParent !== null)
                                : [];

                            if (!currentNav || !currentIndicator || currentLinks.length === 0) {
                                return;
                            }

                            const currentPath = normalizePath(window.location.href);
                            let active = currentLinks[0];

                            currentLinks.forEach((link) => {
                                const path = normalizePath(link.href);
                                const isExact = currentPath === path;
                                const isNested = path !== '/' && currentPath.startsWith(`${path}/`);

                                if (isExact || isNested) {
                                    active = link;
                                }
                            });

                            const navRect = currentNav.getBoundingClientRect();
                            const targetRect = active.getBoundingClientRect();
                            const left = targetRect.left - navRect.left;

                            currentIndicator.style.width = `${targetRect.width}px`;
                            currentIndicator.style.transform = `translate3d(${left}px, -50%, 0)`;
                            currentIndicator.style.opacity = '1';
                        });
                    }
                };

                bindModulesDropdown();
                bindNavIndicator();

                document.addEventListener('livewire:navigated', () => {
                    bindModulesDropdown();
                    bindNavIndicator();
                });
            })();
        </script>

        @fluxScripts
    </body>
</html>
