<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Servicios - Secure Papers</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#F1F7F6]">
        @include('partials.public.header')

        <section class="sp-home-hero-grid-bg relative z-0 bg-gradient-to-br from-[#000F1F] via-[#032221] to-[#006A4C] px-6 pb-24 pt-32 text-[#F1F7F6] md:pb-28 md:pt-36">
            <div class="relative z-10 mx-auto max-w-7xl text-center sp-private-plans-hero animate__animated animate__fadeIn" style="--animate-duration: 3s;">
                <h2 class="mb-6 text-4xl font-bold md:text-5xl">
                    Nuestros <span class="text-[#2CC295]">Servicios</span>
                </h2>
                <p class="mx-auto max-w-3xl text-lg text-[#AAC8C4] md:text-xl">
                    Soluciones integrales para potenciar la calidad, el rigor y el impacto de tus investigaciones cientificas.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-16">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <article class="rounded-2xl border border-[#006A4C]/10 bg-white p-8 shadow-lg transition-shadow hover:shadow-xl">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-xl bg-[#2CC295]/20">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <path d="M14 2v6h6" />
                        </svg>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-[#000F1F]">Revision de Articulos</h3>
                    <p class="text-[#707D7D]">
                        Revision exhaustiva por pares antes del envio a revista. Nos enfocamos en coherencia, flujo logico y claridad en la exposicion de resultados.
                    </p>
                </article>

                <article class="rounded-2xl border border-[#006A4C]/10 bg-white p-8 shadow-lg transition-shadow hover:shadow-xl">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-xl bg-[#00BF81]/20">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.5-3.5" />
                        </svg>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-[#000F1F]">Consultoria Metodologica</h3>
                    <p class="text-[#707D7D]">
                        Asesoria personalizada para asegurar la solidez del diseno de investigacion, seleccion de muestra y validez de los instrumentos.
                    </p>
                </article>

                <article class="rounded-2xl border border-[#006A4C]/10 bg-white p-8 shadow-lg transition-shadow hover:shadow-xl">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-xl bg-[#2FA98C]/20">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9" />
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z" />
                        </svg>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-[#000F1F]">Edicion Cientifica</h3>
                    <p class="text-[#707D7D]">
                        Correccion de estilo, gramatica y formato bajo estandares internacionales (APA, Vancouver, IEEE y otros), manteniendo tono academico.
                    </p>
                </article>

                <article class="rounded-2xl border border-[#006A4C]/10 bg-white p-8 shadow-lg transition-shadow hover:shadow-xl">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-xl bg-[#178760]/20">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3v18h18" />
                            <path d="m19 9-5 5-4-4-3 3" />
                        </svg>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-[#000F1F]">Analisis Estadistico</h3>
                    <p class="text-[#707D7D]">
                        Procesamiento de datos empiricos, aplicacion de pruebas estadisticas adecuadas e interpretacion clara de resultados.
                    </p>
                </article>
            </div>
        </section>

        <section class="mx-auto mb-20 mt-8 max-w-7xl px-6">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-4xl font-bold text-[#000F1F]">Por Que Elegirnos?</h2>
                <p class="text-lg text-[#707D7D]">Compromiso absoluto con la calidad y confidencialidad de tu trabajo</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2 4 6v6c0 5 3.2 9.4 8 10 4.8-.6 8-5 8-10V6z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Confidencialidad Garantizada</h3>
                    <p class="text-[#707D7D]">Tu investigacion esta protegida con estandares altos de seguridad y acuerdos de confidencialidad.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="8" r="6" />
                            <path d="m8.6 13.5 1.4 6 2-1.6 2 1.6 1.4-6" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Revisores Certificados</h3>
                    <p class="text-[#707D7D]">Equipo de expertos con PhD y experiencia en publicaciones cientificas internacionales.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 17 9 11l4 4 8-8" />
                            <path d="M14 7h7v7" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Mejora Comprobable</h3>
                    <p class="text-[#707D7D]">92% de los articulos revisados logran publicacion en revistas indexadas de alto impacto.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 4h14a2 2 0 0 1 2 2v14H4a2 2 0 0 1-2-2z" />
                            <path d="M18 6h2a2 2 0 0 1 2 2v12" />
                            <path d="M6 8h8" />
                            <path d="M6 12h8" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Metodologia Rigurosa</h3>
                    <p class="text-[#707D7D]">Analisis de estructura, metodologia, resultados y referencias bibliograficas.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m12 3 2.9 5.9 6.5 1-4.7 4.6 1.1 6.5L12 18l-5.8 3 1.1-6.5L2.6 10l6.5-1z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Feedback Detallado</h3>
                    <p class="text-[#707D7D]">Comentarios especificos y sugerencias concretas para mejorar tu manuscrito.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Plazos Cumplidos</h3>
                    <p class="text-[#707D7D]">Entrega garantizada en los tiempos acordados sin comprometer calidad.</p>
                </article>
            </div>
        </section>

        @include('partials.public.footer')
    </body>
</html>
