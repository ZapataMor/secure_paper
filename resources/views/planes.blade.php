<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Planes - Secure Papers</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#000F1F] font-sans text-[#F1F7F6] selection:bg-[#2CC295] selection:text-[#000F1F]">
        @php
            $loginUrl = Route::has('login') ? route('login') : '#';
        @endphp

        @include('partials.public.header')

        <section class="relative overflow-hidden px-6 pb-16 pt-32 md:pt-36">
            <div class="pointer-events-none absolute left-1/2 top-0 h-[400px] w-[800px] -translate-x-1/2 rounded-full bg-[#006A4C] opacity-20 blur-[120px]"></div>

            <div class="relative z-10 mx-auto max-w-7xl text-center sp-private-plans-hero animate__animated animate__fadeIn" style="--animate-duration: 3s;">
                <span class="mb-4 block text-sm font-semibold uppercase tracking-wider text-[#2CC295]">Portafolio de Servicios</span>
                <h1 class="mb-6 text-5xl font-bold md:text-6xl">
                    Tu camino seguro a la <br class="hidden md:block">
                    <span class="bg-gradient-to-r from-[#2CC295] to-[#00BF81] bg-clip-text text-transparent">
                        publicacion academica
                    </span>
                </h1>
                <p class="mx-auto max-w-2xl text-xl text-[#AAC8C4]">
                    Elige el plan que mejor se adapte a la etapa de tu investigacion.
                    Garantizamos rigor, confidencialidad y resultados.
                </p>
            </div>
        </section>

        <section class="relative z-10 mx-auto max-w-7xl px-6 pb-24">
            <div class="grid grid-cols-1 items-start gap-8 md:grid-cols-3">
                <article class="rounded-3xl border border-[#006A4C]/30 bg-[#031A19]/60 p-8 backdrop-blur-md transition-colors hover:border-[#006A4C]/60 sp-private-plan-card-left animate__animated animate__fadeInLeft">
                    <h3 class="mb-2 text-2xl font-bold">Esencial</h3>
                    <p class="mb-6 h-10 text-sm text-[#AAC8C4]">Idea inicial, tesis en etapa temprana o consulta rapida.</p>

                    <div class="mb-6">
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-bold">$49</span>
                            <span class="text-[#AAC8C4]">USD</span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-sm text-[#707D7D] line-through">$98 USD</span>
                            <span class="rounded-full bg-[#2CC295]/20 px-2 py-1 text-xs font-medium text-[#2CC295]">50% OFF</span>
                        </div>
                        <p class="mt-1 text-xs text-[#AAC8C4]">~201.000 COP</p>
                    </div>

                    <a href="{{ $loginUrl }}" class="mb-8 block w-full rounded-xl bg-white/10 py-3 text-center font-semibold text-white transition-colors hover:bg-white/20">
                        Empezar ahora
                    </a>

                    <div class="space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Valor principal</p>
                        <ul class="space-y-3 text-sm">
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>1 sesion en vivo (1 hora)</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Conversacion estrategica para ordenar ideas</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Feedback experto</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Grabacion completa de la sesion</span>
                            </li>
                        </ul>
                    </div>
                </article>

                <article class="mt-0 rounded-3xl border border-[#006A4C]/30 bg-[#031A19]/60 p-8 backdrop-blur-md transition-colors hover:border-[#006A4C]/60 md:mt-4 sp-private-plan-card-center animate__animated animate__fadeInUp">
                    <h3 class="mb-2 text-2xl font-bold">Correccion Completa</h3>
                    <p class="mb-6 h-10 text-sm text-[#AAC8C4]">Correccion profunda de tesis o articulo.</p>

                    <div class="mb-6">
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-bold">$299</span>
                            <span class="text-[#AAC8C4]">USD</span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-sm text-[#707D7D] line-through">$399 USD</span>
                            <span class="rounded-full bg-[#2CC295]/20 px-2 py-1 text-xs font-medium text-[#2CC295]">25% OFF</span>
                        </div>
                        <p class="mt-1 text-xs text-[#AAC8C4]">~1.226.000 COP</p>
                    </div>

                    <a href="{{ $loginUrl }}" class="mb-8 block w-full rounded-xl bg-white/10 py-3 text-center font-semibold text-white transition-colors hover:bg-white/20">
                        Empezar ahora
                    </a>

                    <div class="space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Valor principal</p>
                        <ul class="space-y-3 text-sm">
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>2 etapas de trabajo</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Presentacion del caso (1 hora)</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Correccion completa y ajustes via correspondencia</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Manuscrito limpio con comentarios</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Recomendaciones de publicacion</span>
                            </li>
                        </ul>
                    </div>
                </article>

                <article class="relative rounded-3xl border-2 border-[#2CC295] bg-gradient-to-b from-[#006A4C]/30 to-[#031A19]/80 p-8 shadow-[0_0_40px_rgba(44,194,149,0.15)] backdrop-blur-md md:-translate-y-4 sp-private-plan-card-right animate__animated animate__fadeInRight">
                    <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#2CC295] px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-[#000F1F] shadow-lg">
                        El mas recomendado
                    </div>

                    <h3 class="mb-2 text-2xl font-bold">Premium</h3>
                    <p class="mb-6 h-10 text-sm text-[#AAC8C4]">Acompanamiento completo hasta publicacion.</p>

                    <div class="mb-6">
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-bold">$999</span>
                            <span class="text-[#AAC8C4]">USD</span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="rounded-full border border-[#2CC295]/30 bg-[#2CC295]/20 px-2 py-1 text-xs font-medium text-[#2CC295]">Precio de Lanzamiento</span>
                        </div>
                        <p class="mt-1 text-xs text-[#AAC8C4]">~4.096.000 COP</p>
                    </div>

                    <a href="{{ $loginUrl }}" class="mb-8 block w-full rounded-xl bg-[#2CC295] py-3 text-center font-bold text-[#000F1F] shadow-[0_0_20px_rgba(44,194,149,0.3)] transition-colors hover:bg-[#00BF81]">
                        Empezar ahora
                    </a>

                    <div class="space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Valor principal</p>
                        <ul class="space-y-3 text-sm">
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span class="font-medium">5 etapas de acompanamiento</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Analisis Hibrido (Humano + IA)</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Seleccion estrategica de revista</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Simulacion de revision por pares</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">✓</span>
                                <span>Garantia de publicacion (acompanamiento extendido)</span>
                            </li>
                        </ul>
                    </div>
                </article>
            </div>
        </section>

        <section class="relative z-10 mx-auto max-w-5xl px-6 pb-24">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-3xl font-bold">Conoce en detalle cada etapa</h2>
                <p class="text-[#AAC8C4]">Transparencia total en nuestro proceso de trabajo para cada plan.</p>
            </div>

            <div class="space-y-12">
                <article class="overflow-hidden rounded-2xl border border-[#006A4C]/30 bg-[#031A19]/40">
                    <div class="flex items-center gap-3 border-b border-[#006A4C]/30 bg-[#006A4C]/20 px-6 py-4">
                        <div class="h-2 w-2 rounded-full bg-[#2CC295]"></div>
                        <h3 class="text-lg font-bold">1. Paquete Esencial</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-white/5 text-[#AAC8C4]">
                                        <th class="w-24 pb-3 font-medium">Sesion</th>
                                        <th class="w-48 pb-3 font-medium">Momento Temporal</th>
                                        <th class="pb-3 font-medium">Actividades Principales</th>
                                        <th class="w-1/3 pb-3 font-medium">Entregables</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <tr>
                                        <td class="py-4 font-semibold text-[#2CC295]">1</td>
                                        <td class="py-4">Una hora en vivo</td>
                                        <td class="py-4 pr-4">Conversacion estrategica para ordenar ideas, identificar aportes novedosos, feedback experto y valoracion del proyecto.</td>
                                        <td class="py-4 text-[#AAC8C4]">Grabacion completa de la sesion.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex items-start gap-2 rounded-lg bg-white/5 p-3 text-sm text-[#AAC8C4]">
                            <span class="mt-0.5 text-[#2CC295]">i</span>
                            <p><strong>Ideal para:</strong> Primeros pasos o cuando necesitas claridad rapida.</p>
                        </div>
                    </div>
                </article>

                <article class="overflow-hidden rounded-2xl border border-[#006A4C]/30 bg-[#031A19]/40">
                    <div class="flex items-center gap-3 border-b border-[#006A4C]/30 bg-[#006A4C]/20 px-6 py-4">
                        <div class="h-2 w-2 rounded-full bg-[#2CC295]"></div>
                        <h3 class="text-lg font-bold">2. Paquete Correccion Completa</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-white/5 text-[#AAC8C4]">
                                        <th class="w-24 pb-3 font-medium">Sesion</th>
                                        <th class="w-48 pb-3 font-medium">Momento Temporal</th>
                                        <th class="pb-3 font-medium">Actividades Principales</th>
                                        <th class="w-1/3 pb-3 font-medium">Entregables</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <tr>
                                        <td class="py-4 font-semibold text-[#2CC295]">1</td>
                                        <td class="py-4">Dia de contratacion (1 hr)</td>
                                        <td class="py-4 pr-4">Presentacion del caso y necesidades del cliente.</td>
                                        <td class="py-4 text-[#AAC8C4]">Grabacion + entrega del manuscrito para correccion.</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 font-semibold text-[#2CC295]">2</td>
                                        <td class="py-4">Maximo 10 dias</td>
                                        <td class="py-4 pr-4">Correccion completa + ajustes via correspondencia.</td>
                                        <td class="py-4 text-[#AAC8C4]">Manuscrito limpio con comentarios detallados y recomendaciones de publicacion.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex items-start gap-2 rounded-lg bg-white/5 p-3 text-sm text-[#AAC8C4]">
                            <span class="mt-0.5 text-[#2CC295]">i</span>
                            <p><strong>Ideal para:</strong> Tesis o articulos que requieren correccion profunda y listos para entregar.</p>
                        </div>
                    </div>
                </article>

                <article class="overflow-hidden rounded-2xl border border-[#2CC295]/50 bg-gradient-to-r from-[#031A19]/80 to-[#006A4C]/10 shadow-[0_0_20px_rgba(44,194,149,0.05)]">
                    <div class="flex items-center gap-3 border-b border-[#2CC295]/30 bg-gradient-to-r from-[#006A4C]/40 to-transparent px-6 py-4">
                        <div class="h-2 w-2 rounded-full bg-[#2CC295] shadow-[0_0_8px_#2CC295]"></div>
                        <h3 class="text-lg font-bold text-white">
                            3. Paquete Premium
                            <span class="ml-2 text-sm font-normal text-[#2CC295]">(El mas recomendado)</span>
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-white/10 text-[#AAC8C4]">
                                        <th class="w-24 pb-3 font-medium">Etapa</th>
                                        <th class="w-48 pb-3 font-medium">Momento Temporal</th>
                                        <th class="pb-3 font-medium">Actividades Principales</th>
                                        <th class="w-1/3 pb-3 font-medium">Entregables</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <tr>
                                        <td class="py-4 font-semibold text-[#2CC295]">1</td>
                                        <td class="py-4">Dia de contratacion</td>
                                        <td class="py-4 pr-4">Escucha de requerimientos + recepcion de documentos + analisis (Humano + IA).</td>
                                        <td class="py-4 text-[#AAC8C4]">Informe detallado de observaciones al texto original.</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 font-semibold text-[#2CC295]">2</td>
                                        <td class="py-4">2 semanas despues</td>
                                        <td class="py-4 pr-4">Reunion en vivo: discusion + acuerdos + seleccion estrategica de revista.</td>
                                        <td class="py-4 text-[#AAC8C4]">Texto corregido y optimizado listo para envio.</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 font-semibold text-[#2CC295]">3</td>
                                        <td class="py-4">1 semana despues de simulacro</td>
                                        <td class="py-4 pr-4">Reunion en vivo: analisis de comentarios simulados + asesoria de publicacion.</td>
                                        <td class="py-4 text-[#AAC8C4]">Version final + documento de simulacion de revision por pares.</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 font-semibold text-[#2CC295]">4</td>
                                        <td class="py-4">Inmediato</td>
                                        <td class="py-4 pr-4">Envio a revista o unidad academica.</td>
                                        <td class="py-4 text-[#AAC8C4]">Articulo/documento en condiciones optimas.</td>
                                    </tr>
                                    <tr class="bg-[#2CC295]/5">
                                        <td class="py-4 pl-2 font-semibold text-[#2CC295]">5 <span class="text-xs font-normal opacity-70">(Garantia)</span></td>
                                        <td class="py-4">Si es necesario</td>
                                        <td class="py-4 pr-4">Sesion adicional + ajustes para nueva revista.</td>
                                        <td class="py-4 text-[#AAC8C4]">Nuevo acompanamiento hasta publicacion exitosa.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <div class="border-t border-white/5">
            @include('partials.public.footer')
        </div>
    </body>
</html>
