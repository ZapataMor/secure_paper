<x-layouts::app :title="__('Inicio')">
    <section class="sp-admin-page">
        @if(auth()->user()?->isAdmin())
            <header class="sp-admin-page-header">
                <p class="sp-admin-page-kicker">Panel principal</p>
                <h1>Modulos del sistema</h1>
                <p>Selecciona el modulo que deseas gestionar desde la barra superior o desde las tarjetas.</p>
            </header>

            <div class="sp-admin-modules-grid">
                <article class="sp-admin-module-card">
                    <span class="sp-admin-module-badge">Modulo 01</span>
                    <h2>Gestion de usuarios</h2>
                    <p>Administra la informacion de los usuarios registrados en Secure Papers.</p>
                    <a href="{{ route('admin.users.index') }}" class="sp-admin-module-action" wire:navigate>
                        Abrir modulo
                    </a>
                </article>
            </div>
        @else
            @php
                $hasActiveMembership = auth()->user()?->hasActiveMembership() ?? false;
                $activePlanName = auth()->user()?->activeMembershipPlanName();
            @endphp

            <article class="sp-user-home-card animate__animated animate__fadeInDownBig">
                <h1 class="mb-6 text-5xl font-bold md:text-6xl">
                    Hola, bienvenido a <br class="hidden md:block">
                    <span class="bg-gradient-to-r from-[#2CC295] to-[#00BF81] bg-clip-text text-transparent">
                        Professional Papers
                    </span>
                </h1>
                <p class="mx-auto max-w-2xl text-xl text-[#AAC8C4]">Si te encuentras en el desarrollo de tu proyecto y necesitas apoyo, ideas, revisiones u orientacion, estas en el lugar indicado.</p>
                <p class="mx-auto max-w-2xl text-xl text-[#AAC8C4]">Para comenzar, dirigete al modulo "Planes" y elige la opcion que mejor se adapte a tus necesidades. Luego, podras cargar tu proyecto en el modulo "Cargue de documentos" y empezar a recibir acompanamiento en cada etapa de tu proceso.</p>

                <section class="sp-user-guide" aria-labelledby="sp-user-guide-title">
                    <div class="sp-user-guide-shell">
                        <header class="sp-user-guide-header">
                            <h2 id="sp-user-guide-title">Guia de uso inicial</h2>
                            <p>Sigue esta secuencia sencilla para comenzar el proceso de acompanamiento de tu proyecto.</p>
                        </header>

                        <ol class="sp-user-guide-list">
                            <li class="sp-user-guide-item is-current">
                                <span class="sp-user-guide-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 7v14" />
                                        <path d="M3 18h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6V4h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3H3z" />
                                    </svg>
                                </span>

                                <article class="sp-user-guide-card">
                                    <div class="sp-user-guide-meta">
                                        <div class="sp-user-guide-title-wrap">
                                            <p class="sp-user-guide-step-label">Paso 1</p>
                                            <h3 class="sp-user-guide-title">Elige tu plan</h3>
                                        </div>
                                        <a href="{{ route('private.planes') }}" class="sp-user-guide-action" wire:navigate>
                                            Ver planes
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M5 12h14" />
                                                <path d="m13 5 7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                    <p class="sp-user-guide-description">
                                        Dirigete al modulo "Planes", donde podras revisar y seleccionar el plan que mejor se ajuste a tus necesidades (Esencial, Completa o Premium).
                                    </p>
                                </article>
                            </li>

                            <li class="sp-user-guide-item is-pending">
                                <span class="sp-user-guide-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 12v9" />
                                        <path d="m16 16-4-4-4 4" />
                                        <path d="M20.39 18.39A5.5 5.5 0 0 0 18 8h-1.26A8 8 0 1 0 3 16.3" />
                                    </svg>
                                </span>

                                <article class="sp-user-guide-card">
                                    <div class="sp-user-guide-meta">
                                        <div class="sp-user-guide-title-wrap">
                                            <p class="sp-user-guide-step-label">Paso 2</p>
                                            <h3 class="sp-user-guide-title">Carga tus documentos</h3>
                                        </div>

                                        @if($hasActiveMembership)
                                            <a href="{{ route('private.upload-document') }}" class="sp-user-guide-action is-secondary" wire:navigate>
                                                Ir a cargar
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M5 12h14" />
                                                    <path d="m13 5 7 7-7 7" />
                                                </svg>
                                            </a>
                                        @else
                                            <button
                                                type="button"
                                                disabled
                                                aria-disabled="true"
                                                class="sp-user-guide-action is-secondary cursor-not-allowed border-[#3D5753]/70 bg-[#062B29]/80 text-[#8CA9A4] opacity-65"
                                            >
                                                Ir a cargar (bloqueado)
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <rect x="3" y="11" width="18" height="10" rx="2" />
                                                    <path d="M7 11V8a5 5 0 0 1 10 0v3" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="sp-user-guide-description">
                                        Accede a "Cargar documento". Sube tu archivo y agrega las indicaciones u observaciones especificas que deseas que sean revisadas.
                                    </p>
                                    @if(! $hasActiveMembership)
                                        <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-[#F9BC60]">
                                            Requiere una membresia activa y pagada.
                                        </p>
                                    @elseif($activePlanName)
                                        <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-[#2CC295]">
                                            Plan activo: {{ $activePlanName }}
                                        </p>
                                    @endif
                                </article>
                            </li>

                            <li class="sp-user-guide-item is-pending">
                                <span class="sp-user-guide-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="7" />
                                        <path d="m20 20-3.5-3.5" />
                                    </svg>
                                </span>

                                <article class="sp-user-guide-card">
                                    <div class="sp-user-guide-title-wrap">
                                        <p class="sp-user-guide-step-label">Paso 3</p>
                                        <h3 class="sp-user-guide-title">Analisis experto</h3>
                                    </div>
                                    <p class="sp-user-guide-description">
                                        Una vez enviado, nuestro equipo iniciara el proceso de analisis segun el plan elegido. Te notificaremos cuando haya avances.
                                    </p>
                                </article>
                            </li>

                            <li class="sp-user-guide-item is-pending">
                                <span class="sp-user-guide-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                    </svg>
                                </span>

                                <article class="sp-user-guide-card">
                                    <div class="sp-user-guide-title-wrap">
                                        <p class="sp-user-guide-step-label">Paso 4</p>
                                        <h3 class="sp-user-guide-title">Retroalimentacion y reunion</h3>
                                    </div>
                                    <p class="sp-user-guide-description">
                                        Recibiras las correcciones, comentarios detallados y la programacion de una reunion con tu asesor (dependiendo del plan).
                                    </p>
                                </article>
                            </li>

                            <li class="sp-user-guide-item is-pending">
                                <span class="sp-user-guide-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m12 2 2.95 5.98 6.6.96-4.78 4.66 1.13 6.57L12 17.08 6.1 20.17l1.13-6.57-4.78-4.66 6.6-.96z" />
                                    </svg>
                                </span>

                                <article class="sp-user-guide-card">
                                    <div class="sp-user-guide-title-wrap">
                                        <p class="sp-user-guide-step-label">Paso 5</p>
                                        <h3 class="sp-user-guide-title">Garantia y acompanamiento</h3>
                                    </div>
                                    <p class="sp-user-guide-description">
                                        Este flujo garantiza una atencion organizada hasta alcanzar tus objetivos academicos.
                                    </p>
                                </article>
                            </li>
                        </ol>
                    </div>
                </section>
            </article>
        @endif
    </section>
</x-layouts::app>
