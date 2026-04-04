<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Public dashboard for Secure Papers</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#F1F7F6]">
        @php
            $loginUrl = Route::has('login') ? route('login') : '#';
            $registerUrl = Route::has('register') ? route('register') : $loginUrl;
        @endphp

        <header class="bg-[#000F1F] px-6 py-4 text-[#F1F7F6] shadow-lg">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-[#2CC295] p-2">
                        <svg class="h-8 w-8 text-[#000F1F]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2 4 6v6c0 5 3.2 9.4 8 10 4.8-.6 8-5 8-10V6z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Secure Papers</h1>
                        <p class="text-sm text-[#AAC8C4]">Academic Excellence & Trust</p>
                    </div>
                </div>

                <nav class="flex flex-wrap items-center gap-3 sm:gap-6">
                    <a href="#servicios" class="text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Servicios</a>
                    <a href="#nosotros" class="text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Nosotros</a>
                    <a href="{{ $loginUrl }}" class="rounded-lg bg-[#006A4C] px-6 py-2 text-[#F1F7F6] transition-colors hover:bg-[#00BF81]">Contactar</a>
                </nav>
            </div>
        </header>

        <section class="bg-gradient-to-br from-[#000F1F] via-[#032221] to-[#006A4C] px-6 py-20 text-[#F1F7F6]">
            <div class="mx-auto max-w-7xl text-center">
                <h2 class="mb-6 text-4xl font-bold md:text-5xl">
                    Garantizamos la <span class="text-[#2CC295]">Excelencia Academica</span> de tu Investigacion
                </h2>
                <p class="mx-auto mb-8 max-w-3xl text-lg text-[#AAC8C4] md:text-xl">
                    Revision profesional de articulos cientificos y proyectos de investigacion con los mas altos estandares de calidad
                </p>
                <a href="{{ $registerUrl }}" class="inline-block rounded-lg bg-[#2CC295] px-8 py-4 text-lg font-semibold text-[#000F1F] shadow-lg transition-colors hover:bg-[#00BF81]">
                    Solicitar Revision
                </a>
            </div>
        </section>

        <section class="mx-auto -mt-12 mb-16 max-w-7xl px-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <article class="rounded-xl border border-[#AAC8C4]/30 bg-white p-6 shadow-md transition-shadow hover:shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="rounded-lg bg-[#2CC295]/20 p-3">
                            <svg class="h-6 w-6 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mb-1 text-3xl font-bold text-[#000F1F]">2,847</div>
                    <div class="text-sm text-[#707D7D]">Articulos Revisados</div>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/30 bg-white p-6 shadow-md transition-shadow hover:shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="rounded-lg bg-[#00BF81]/20 p-3">
                            <svg class="h-6 w-6 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M7 10v12"/>
                                <path d="M15 5.9 14 13h4.8a2 2 0 0 0 2-1.7l1-5A2 2 0 0 0 19.8 4H16a1 1 0 0 0-1 .9Z"/>
                                <path d="M7 10 9.6 4.8A2 2 0 0 1 11.4 4H14"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mb-1 text-3xl font-bold text-[#000F1F]">98.5%</div>
                    <div class="text-sm text-[#707D7D]">Tasa de Satisfaccion</div>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/30 bg-white p-6 shadow-md transition-shadow hover:shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="rounded-lg bg-[#2FA98C]/20 p-3">
                            <svg class="h-6 w-6 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <path d="M20 8v6"/>
                                <path d="M23 11h-6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mb-1 text-3xl font-bold text-[#000F1F]">456+</div>
                    <div class="text-sm text-[#707D7D]">Investigadores Confiando</div>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/30 bg-white p-6 shadow-md transition-shadow hover:shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="rounded-lg bg-[#178760]/20 p-3">
                            <svg class="h-6 w-6 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mb-1 text-3xl font-bold text-[#000F1F]">72h</div>
                    <div class="text-sm text-[#707D7D]">Tiempo Promedio de Entrega</div>
                </article>
            </div>
        </section>

        <section id="servicios" class="mx-auto mb-16 max-w-7xl px-6">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-4xl font-bold text-[#000F1F]">Por Que Elegirnos?</h2>
                <p class="text-lg text-[#707D7D]">Compromiso absoluto con la calidad y confidencialidad de tu trabajo</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2 4 6v6c0 5 3.2 9.4 8 10 4.8-.6 8-5 8-10V6z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Confidencialidad Garantizada</h3>
                    <p class="text-[#707D7D]">Tu investigacion esta protegida con los mas altos estandares de seguridad y acuerdos de confidencialidad.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="8" r="6"/>
                            <path d="m8.6 13.5 1.4 6 2-1.6 2 1.6 1.4-6"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Revisores Certificados</h3>
                    <p class="text-[#707D7D]">Equipo de expertos con PhD y amplia experiencia en publicaciones cientificas internacionales.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 17 9 11l4 4 8-8"/>
                            <path d="M14 7h7v7"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Mejora Comprobable</h3>
                    <p class="text-[#707D7D]">92% de los articulos revisados logran publicacion en revistas indexadas de alto impacto.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 4h14a2 2 0 0 1 2 2v14H4a2 2 0 0 1-2-2z"/>
                            <path d="M18 6h2a2 2 0 0 1 2 2v12"/>
                            <path d="M6 8h8"/>
                            <path d="M6 12h8"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Metodologia Rigurosa</h3>
                    <p class="text-[#707D7D]">Analisis exhaustivo de estructura, metodologia, resultados y referencias bibliograficas.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m12 3 2.9 5.9 6.5 1-4.7 4.6 1.1 6.5L12 18l-5.8 3 1.1-6.5L2.6 10l6.5-1z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Feedback Detallado</h3>
                    <p class="text-[#707D7D]">Recibe comentarios especificos y sugerencias concretas para mejorar tu manuscrito.</p>
                </article>

                <article class="rounded-xl border border-[#AAC8C4]/20 bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-lg bg-gradient-to-br from-[#2CC295]/20 to-[#006A4C]/20 p-3">
                        <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-[#000F1F]">Plazos Cumplidos</h3>
                    <p class="text-[#707D7D]">Entrega garantizada en los tiempos acordados sin comprometer la calidad de la revision.</p>
                </article>
            </div>
        </section>

        <section id="nosotros" class="bg-white px-6 py-16">
            <div class="mx-auto max-w-7xl">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-4xl font-bold text-[#000F1F]">Nuestro Proceso de Revision</h2>
                    <p class="text-lg text-[#707D7D]">Un proceso sistematico y transparente en 4 pasos</p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <article class="group relative rounded-xl bg-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="absolute -left-4 -top-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#006A4C] text-lg font-bold text-[#F1F7F6] shadow-lg">1</div>
                        <div class="mb-4 flex justify-center">
                            <div class="rounded-lg bg-[#F1F7F6] p-4 transition-colors group-hover:bg-[#2CC295]/20">
                                <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <path d="M14 2v6h6"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="mb-2 text-center text-xl font-semibold text-[#000F1F]">Envio Seguro</h3>
                        <p class="text-center text-[#707D7D]">Sube tu documento a traves de nuestra plataforma encriptada</p>
                    </article>

                    <article class="group relative rounded-xl bg-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="absolute -left-4 -top-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#006A4C] text-lg font-bold text-[#F1F7F6] shadow-lg">2</div>
                        <div class="mb-4 flex justify-center">
                            <div class="rounded-lg bg-[#F1F7F6] p-4 transition-colors group-hover:bg-[#2CC295]/20">
                                <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m20 20-3.5-3.5"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="mb-2 text-center text-xl font-semibold text-[#000F1F]">Analisis Profundo</h3>
                        <p class="text-center text-[#707D7D]">Revision exhaustiva por expertos en tu area de investigacion</p>
                    </article>

                    <article class="group relative rounded-xl bg-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="absolute -left-4 -top-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#006A4C] text-lg font-bold text-[#F1F7F6] shadow-lg">3</div>
                        <div class="mb-4 flex justify-center">
                            <div class="rounded-lg bg-[#F1F7F6] p-4 transition-colors group-hover:bg-[#2CC295]/20">
                                <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="mb-2 text-center text-xl font-semibold text-[#000F1F]">Reporte Detallado</h3>
                        <p class="text-center text-[#707D7D]">Recibe feedback estructurado con sugerencias especificas</p>
                    </article>

                    <article class="group relative rounded-xl bg-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="absolute -left-4 -top-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#006A4C] text-lg font-bold text-[#F1F7F6] shadow-lg">4</div>
                        <div class="mb-4 flex justify-center">
                            <div class="rounded-lg bg-[#F1F7F6] p-4 transition-colors group-hover:bg-[#2CC295]/20">
                                <svg class="h-8 w-8 text-[#006A4C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="mb-2 text-center text-xl font-semibold text-[#000F1F]">Seguimiento</h3>
                        <p class="text-center text-[#707D7D]">Acompanamiento continuo hasta la version final</p>
                    </article>
                </div>
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
                    <a href="{{ $registerUrl }}" class="rounded-lg bg-[#F1F7F6] px-8 py-4 text-lg font-semibold text-[#006A4C] shadow-lg transition-colors hover:bg-white">
                        Comenzar Ahora
                    </a>
                    <a href="{{ $loginUrl }}" class="rounded-lg border-2 border-[#F1F7F6] px-8 py-4 text-lg font-semibold text-[#F1F7F6] transition-colors hover:bg-[#F1F7F6] hover:text-[#006A4C]">
                        Ver Planes y Precios
                    </a>
                </div>
            </div>
        </section>

        <footer class="bg-[#000F1F] px-6 py-12 text-[#F1F7F6]">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 grid grid-cols-1 gap-8 md:grid-cols-4">
                    <div>
                        <h3 class="mb-4 font-semibold text-[#2CC295]">Secure Papers</h3>
                        <p class="text-sm text-[#AAC8C4]">Excelencia academica respaldada por expertos</p>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Servicios</h4>
                        <ul class="space-y-2 text-sm text-[#AAC8C4]">
                            <li>Revision de Articulos</li>
                            <li>Consultoria Metodologica</li>
                            <li>Edicion Cientifica</li>
                            <li>Analisis Estadistico</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Recursos</h4>
                        <ul class="space-y-2 text-sm text-[#AAC8C4]">
                            <li>Guias de Publicacion</li>
                            <li>Blog Academico</li>
                            <li>Preguntas Frecuentes</li>
                            <li>Contacto</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Legal</h4>
                        <ul class="space-y-2 text-sm text-[#AAC8C4]">
                            <li>Terminos y Condiciones</li>
                            <li>Politica de Privacidad</li>
                            <li>Confidencialidad</li>
                            <li>Certificaciones</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-[#006A4C] pt-8 text-center text-sm text-[#AAC8C4]">
                    <p>&copy; 2026 Secure Papers. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
