<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nosotros - Secure Papers</title>

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
                    Sobre <span class="text-[#2CC295]">Nosotros</span>
                </h2>
                <p class="mx-auto max-w-3xl text-lg text-[#AAC8C4] md:text-xl">
                    Somos un equipo de expertos comprometidos con la excelencia academica, impulsando la diseminacion del conocimiento.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-20">
            <div class="grid grid-cols-1 items-center gap-12 md:grid-cols-2">
                <div>
                    <h3 class="mb-6 text-3xl font-bold text-[#000F1F]">Nuestra Mision</h3>
                    <p class="mb-6 text-lg leading-relaxed text-[#707D7D]">
                        En Secure Papers creemos que el conocimiento cientifico debe ser accesible y de alta calidad. Trabajamos junto a investigadores,
                        academicos y estudiantes para asegurar que sus contribuciones cumplan con exigencias del panorama cientifico actual.
                    </p>
                    <p class="text-lg leading-relaxed text-[#707D7D]">
                        Garantizamos confidencialidad absoluta y aplicamos rigor tecnico para mejorar cada manuscrito, buscando su exito en revistas prestigiosas.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <article class="rounded-2xl border border-[#006A4C]/10 bg-white p-6 text-center shadow-lg">
                        <svg class="mx-auto mb-3 h-10 w-10 text-[#2CC295]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="8.5" cy="7" r="4" />
                            <path d="M20 8v6" />
                            <path d="M23 11h-6" />
                        </svg>
                        <h4 class="font-bold text-[#000F1F]">Red Global</h4>
                        <p class="mt-2 text-sm text-[#707D7D]">Expertos en 20+ paises</p>
                    </article>

                    <article class="rounded-2xl border border-[#006A4C]/10 bg-white p-6 text-center shadow-lg">
                        <svg class="mx-auto mb-3 h-10 w-10 text-[#00BF81]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="8" r="6" />
                            <path d="m8.6 13.5 1.4 6 2-1.6 2 1.6 1.4-6" />
                        </svg>
                        <h4 class="font-bold text-[#000F1F]">PhD. y PostDocs</h4>
                        <p class="mt-2 text-sm text-[#707D7D]">Revisores cualificados</p>
                    </article>

                    <article class="col-span-2 rounded-2xl border border-[#006A4C]/10 bg-white p-6 text-center shadow-lg">
                        <svg class="mx-auto mb-3 h-10 w-10 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2" />
                            <path d="M16 7V5a4 4 0 0 0-8 0v2" />
                        </svg>
                        <h4 class="font-bold text-[#000F1F]">10+ Anos de Experiencia</h4>
                        <p class="mt-2 text-sm text-[#707D7D]">Apoyando a la comunidad cientifica internacional.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-white px-6 py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-4xl font-bold text-[#000F1F]">Nuestro Proceso de Revision</h2>
                    <p class="text-lg text-[#707D7D]">Un proceso sistematico y transparente en 4 pasos fundamentales</p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <article class="group relative rounded-xl bg-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="absolute -left-4 -top-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#006A4C] text-lg font-bold text-[#F1F7F6] shadow-lg">1</div>
                        <div class="mb-4 flex justify-center">
                            <div class="rounded-lg bg-[#F1F7F6] p-4 transition-colors group-hover:bg-[#2CC295]/20">
                                <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <path d="M14 2v6h6" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mb-2 text-center text-xl font-semibold text-[#000F1F]">Envio Seguro</h3>
                        <p class="text-center text-[#707D7D]">Sube tu documento en una plataforma encriptada de forma agil y segura.</p>
                    </article>

                    <article class="group relative rounded-xl bg-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="absolute -left-4 -top-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#006A4C] text-lg font-bold text-[#F1F7F6] shadow-lg">2</div>
                        <div class="mb-4 flex justify-center">
                            <div class="rounded-lg bg-[#F1F7F6] p-4 transition-colors group-hover:bg-[#2CC295]/20">
                                <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7" />
                                    <path d="m20 20-3.5-3.5" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mb-2 text-center text-xl font-semibold text-[#000F1F]">Analisis Profundo</h3>
                        <p class="text-center text-[#707D7D]">Revision exhaustiva por especialistas en tu area de investigacion.</p>
                    </article>

                    <article class="group relative rounded-xl bg-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="absolute -left-4 -top-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#006A4C] text-lg font-bold text-[#F1F7F6] shadow-lg">3</div>
                        <div class="mb-4 flex justify-center">
                            <div class="rounded-lg bg-[#F1F7F6] p-4 transition-colors group-hover:bg-[#2CC295]/20">
                                <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mb-2 text-center text-xl font-semibold text-[#000F1F]">Reporte Detallado</h3>
                        <p class="text-center text-[#707D7D]">Recibe feedback estructurado con sugerencias puntuales para mejorar.</p>
                    </article>

                    <article class="group relative rounded-xl bg-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="absolute -left-4 -top-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#006A4C] text-lg font-bold text-[#F1F7F6] shadow-lg">4</div>
                        <div class="mb-4 flex justify-center">
                            <div class="rounded-lg bg-[#F1F7F6] p-4 transition-colors group-hover:bg-[#2CC295]/20">
                                <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mb-2 text-center text-xl font-semibold text-[#000F1F]">Seguimiento</h3>
                        <p class="text-center text-[#707D7D]">Acompanamiento y resolucion de dudas hasta la version final.</p>
                    </article>
                </div>
            </div>
        </section>

        @include('partials.public.footer')
    </body>
</html>
