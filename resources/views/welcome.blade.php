<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Professional Papers</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#F1F7F6]">
        @php
            $loginUrl = Route::has('login') ? route('login') : '#';
            $plansUrl = Route::has('plans') ? route('plans') : url('/planes');
        @endphp

        @include('partials.public.header')

        <section class="sp-home-hero-grid-bg relative z-0 bg-gradient-to-br from-[#000F1F] via-[#032221] to-[#006A4C] px-6 pb-24 pt-32 text-[#F1F7F6] md:pb-28 md:pt-36">
            <div class="relative z-10 mx-auto max-w-7xl text-center sp-private-plans-hero animate__animated animate__fadeIn" style="--animate-duration: 3s;">
                <h2 class="mb-6 text-4xl font-bold md:text-5xl">
                    Garantizamos la <span class="text-[#2CC295]">Excelencia Academica</span> de tu Investigacion
                </h2>
                <p class="mx-auto mb-8 max-w-3xl text-lg text-[#AAC8C4] md:text-xl">
                    Revision profesional de articulos cientificos y proyectos de investigacion con los mas altos estandares de calidad
                </p>
                <a href="{{ $loginUrl }}" class="inline-block rounded-lg bg-[#2CC295] px-8 py-4 text-lg font-semibold text-[#000F1F] shadow-lg transition-colors hover:bg-[#00BF81]">
                    Solicitar Revision
                </a>
            </div>
        </section>

        <section class="relative z-20 mx-auto -mt-12 mb-16 max-w-7xl px-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <article class="rounded-xl border border-[#AAC8C4]/30 bg-white p-6 shadow-md transition-shadow hover:shadow-xl">
                    <div class="mb-4 rounded-lg bg-[#2CC295]/20 p-3 w-fit">
                        <svg class="h-6 w-6 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <path d="M14 2v6h6" />
                        </svg>
                    </div>
                    <div class="mb-1 text-3xl font-bold text-[#000F1F]">2,847</div>
                    <div class="text-sm text-[#707D7D]">Articulos Revisados</div>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/30 bg-white p-6 shadow-md transition-shadow hover:shadow-xl">
                    <div class="mb-4 rounded-lg bg-[#00BF81]/20 p-3 w-fit">
                        <svg class="h-6 w-6 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M7 10v12" />
                            <path d="M15 5.9 14 13h4.8a2 2 0 0 0 2-1.7l1-5A2 2 0 0 0 19.8 4H16a1 1 0 0 0-1 .9Z" />
                            <path d="M7 10 9.6 4.8A2 2 0 0 1 11.4 4H14" />
                        </svg>
                    </div>
                    <div class="mb-1 text-3xl font-bold text-[#000F1F]">98.5%</div>
                    <div class="text-sm text-[#707D7D]">Tasa de Satisfaccion</div>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/30 bg-white p-6 shadow-md transition-shadow hover:shadow-xl">
                    <div class="mb-4 rounded-lg bg-[#2FA98C]/20 p-3 w-fit">
                        <svg class="h-6 w-6 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="8.5" cy="7" r="4" />
                            <path d="M20 8v6" />
                            <path d="M23 11h-6" />
                        </svg>
                    </div>
                    <div class="mb-1 text-3xl font-bold text-[#000F1F]">456+</div>
                    <div class="text-sm text-[#707D7D]">Investigadores Confiando</div>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/30 bg-white p-6 shadow-md transition-shadow hover:shadow-xl">
                    <div class="mb-4 rounded-lg bg-[#178760]/20 p-3 w-fit">
                        <svg class="h-6 w-6 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                    </div>
                    <div class="mb-1 text-3xl font-bold text-[#000F1F]">72h</div>
                    <div class="text-sm text-[#707D7D]">Tiempo Promedio de Entrega</div>
                </article>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-16">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-4xl font-bold text-[#000F1F]">Lo Que Dicen Nuestros Clientes</h2>
                <p class="text-lg text-[#707D7D]">La confianza de investigadores de todo el mundo</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <article class="rounded-xl border-l-4 border-[#2CC295] bg-white p-6 shadow-md transition-shadow hover:shadow-lg">
                    <div class="mb-4 flex gap-1 text-[#2CC295]">
                        <span>*****</span>
                    </div>
                    <p class="mb-4 italic text-[#000F1F]">"La revision fue extraordinariamente detallada. Las sugerencias mejoraron significativamente la calidad de mi articulo. Publicado en Q1."</p>
                    <div class="border-t border-[#AAC8C4] pt-4">
                        <div class="font-semibold text-[#000F1F]">Dr. Maria Gonzalez</div>
                        <div class="text-sm text-[#707D7D]">Investigadora Senior</div>
                        <div class="text-sm text-[#006A4C]">Universidad Nacional</div>
                    </div>
                </article>

                <article class="rounded-xl border-l-4 border-[#2CC295] bg-white p-6 shadow-md transition-shadow hover:shadow-lg">
                    <div class="mb-4 flex gap-1 text-[#2CC295]">
                        <span>*****</span>
                    </div>
                    <p class="mb-4 italic text-[#000F1F]">"Profesionalismo absoluto. El equipo identifico aspectos metodologicos que habiamos pasado por alto. Muy recomendado."</p>
                    <div class="border-t border-[#AAC8C4] pt-4">
                        <div class="font-semibold text-[#000F1F]">Prof. Carlos Ramirez</div>
                        <div class="text-sm text-[#707D7D]">Director de Investigacion</div>
                        <div class="text-sm text-[#006A4C]">Instituto Tecnologico</div>
                    </div>
                </article>

                <article class="rounded-xl border-l-4 border-[#2CC295] bg-white p-6 shadow-md transition-shadow hover:shadow-lg">
                    <div class="mb-4 flex gap-1 text-[#2CC295]">
                        <span>*****</span>
                    </div>
                    <p class="mb-4 italic text-[#000F1F]">"Excelente servicio. La retroalimentacion fue constructiva y me ayudo a fortalecer mi marco teorico considerablemente."</p>
                    <div class="border-t border-[#AAC8C4] pt-4">
                        <div class="font-semibold text-[#000F1F]">Dra. Ana Patricia Silva</div>
                        <div class="text-sm text-[#707D7D]">Candidata Doctoral</div>
                        <div class="text-sm text-[#006A4C]">Universidad de Chile</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="bg-gradient-to-r from-[#006A4C] to-[#00BF81] px-6 py-16 text-[#F1F7F6]">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="mb-6 text-4xl font-bold">Listo para Asegurar la Calidad de tu Investigacion?</h2>
                <p class="mb-8 text-xl text-[#F1F7F6]/90">
                    Unete a cientos de investigadores que confian en Secure Papers para llevar su trabajo al siguiente nivel
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ $loginUrl }}" class="rounded-lg bg-[#F1F7F6] px-8 py-4 text-lg font-semibold text-[#006A4C] shadow-lg transition-colors hover:bg-white">
                        Comenzar Ahora
                    </a>
                    <a href="{{ $plansUrl }}" class="rounded-lg border-2 border-[#F1F7F6] px-8 py-4 text-lg font-semibold text-[#F1F7F6] transition-colors hover:bg-[#F1F7F6] hover:text-[#006A4C]">
                        Ver Planes y Precios
                    </a>
                </div>
            </div>
        </section>

        @include('partials.public.footer')
    </body>
</html>
