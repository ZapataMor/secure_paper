<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Professional Papers</title>

        <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('assets/images/logo.png') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .plans-carousel {
                overflow: hidden;
                position: relative;
            }

            .plans-track {
                display: flex;
                width: max-content;
                animation: scrollPlans 24s linear infinite;
                will-change: transform;
            }

            .plans-track:hover {
                animation-play-state: paused;
            }

            .plans-carousel-set {
                align-items: stretch;
                display: flex;
                gap: 1.5rem;
                padding-right: 1.5rem;
            }

            .plan-card-home {
                flex: 0 0 calc((min(100vw, 88rem) - 4.5rem) / 2.55);
                min-width: 0;
            }

            .home-service-phrases {
                display: grid;
                min-height: 4.5rem;
                place-items: center;
            }

            .home-service-phrase {
                grid-area: 1 / 1;
                max-width: 48rem;
                opacity: 0;
                animation: rotateServicePhrase 18s ease-in-out infinite;
            }

            .home-service-phrase:nth-child(2) {
                animation-delay: 6s;
            }

            .home-service-phrase:nth-child(3) {
                animation-delay: 12s;
            }

            @keyframes scrollPlans {
                from {
                    transform: translateX(0);
                }

                to {
                    transform: translateX(-50%);
                }
            }

            @keyframes rotateServicePhrase {
                0%,
                10% {
                    opacity: 0;
                    transform: translateY(0.35rem);
                }

                18%,
                40% {
                    opacity: 1;
                    transform: translateY(0);
                }

                50%,
                100% {
                    opacity: 0;
                    transform: translateY(-0.35rem);
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .home-service-phrase {
                    animation: none;
                    opacity: 0;
                    transform: none;
                }

                .home-service-phrase:first-child {
                    opacity: 1;
                }
            }

            @media (max-width: 1024px) {
                .plan-card-home {
                    flex-basis: calc((100vw - 3rem) / 1.45);
                }
            }

            @media (max-width: 640px) {
                .plan-card-home {
                    flex-basis: min(92vw, 30rem);
                }
            }
        </style>
    </head>
    <body class="min-h-screen overflow-x-hidden bg-[#F1F7F6] text-[17px]">
        @php
            $loginUrl = Route::has('login') ? route('login') : '#';
            $plansUrl = Route::has('plans') ? route('plans') : url('/planes');
            $homePlanCards = [
                [
                    'name' => 'Paquete Asesoría Face to Face',
                    'anchor' => 'asesoria-esencial',
                    'tag' => 'Ideal para iniciar',
                    'secondary_badge' => 'Más accesible',
                    'price' => 'Desde 225 USD + IVA',
                    'discount' => '25% OFF',
                    'description' => 'Ordena tus ideas, recibe feedback experto y avanza con confianza en el momento clave de tu proyecto.',
                    'benefits' => [
                        'Sesión sincrónica estratégica de 60 minutos',
                        'Feedback experto con Inteligencia Híbrida',
                        'Identificación y desarrollo de ideas fuerza',
                        'Transcripción completa con comentarios',
                        'Mapa conceptual y plan de acción inmediato',
                    ],
                    'featured' => false,
                ],
                [
                    'name' => 'Paquete Corrección Completa',
                    'anchor' => 'correccion-completa',
                    'tag' => 'Más demandado',
                    'secondary_badge' => null,
                    'price' => 'Desde 650 USD + IVA',
                    'discount' => null,
                    'description' => 'Pulimos y perfeccionamos tu manuscrito hasta alcanzar los estándares académicos más exigentes.',
                    'benefits' => [
                        'Corrección completa (ortografía, estilo, coherencia y argumentación)',
                        'Formato APA 7 o el exigido por tu universidad',
                        'Comentarios detallados del experto',
                        'Sesión sincrónica de retroalimentación',
                        'Plazos Estándar, Express o Turbo',
                    ],
                    'featured' => false,
                ],
                [
                    'name' => 'Paquete Redacción Profesional',
                    'anchor' => 'redaccion-express-profesional',
                    'tag' => 'Desde cero',
                    'secondary_badge' => null,
                    'price' => 'Desde 0,14 USD por palabra (Pregrado)',
                    'discount' => null,
                    'description' => 'Redacción experta y de alto nivel a partir de tus ideas, datos y materiales.',
                    'benefits' => [
                        'Redacción completa con Inteligencia Híbrida',
                        'Estructura académica impecable',
                        'Corrección y optimización integral',
                        'Sesión sincrónica de planeación',
                        'Versión lista para sustentación o envío',
                    ],
                    'featured' => false,
                ],
                [
                    'name' => 'Paquete Segundo Tutor',
                    'anchor' => 'segundo-tutor',
                    'tag' => 'Acompañamiento continuo',
                    'secondary_badge' => null,
                    'price' => 'Desde 900 USD/mes (Pregrado) + IVA',
                    'discount' => '10% OFF',
                    'description' => 'Apoyo mensual estructurado durante 3 meses para tesis, monografías y trabajos de grado.',
                    'benefits' => [
                        'Sesiones sincrónicas mensuales',
                        'Corrección completa de capítulos',
                        'Desarrollo de ideas fuerza y rigor metodológico',
                        'Productos tangibles cada mes',
                        'Alineación total con tu tutor y facultad',
                    ],
                    'featured' => false,
                ],
                [
                    'name' => 'Paquete Modo Indexación Publicación Científica',
                    'anchor' => 'premium-publicacion-cientifica',
                    'tag' => 'Más completo',
                    'secondary_badge' => 'Recomendado',
                    'price' => 'Desde 950 USD + IVA',
                    'discount' => '10% OFF',
                    'description' => 'Acompañamiento integral para preparar, optimizar y enviar tu manuscrito a publicación en revistas indexadas.',
                    'benefits' => [
                        'Selección estratégica de revista',
                        'Texto corregido y optimizado',
                        'Revisión simulada por pares',
                        'Ajustes post-revisión',
                        'Acompañamiento hasta publicación (1 año en Línea Premium)',
                    ],
                    'featured' => true,
                ],
            ];
        @endphp

        @include('partials.public.header')

        <section class="sp-home-hero-grid-bg relative z-0 bg-gradient-to-br from-[#000F1F] via-[#032221] to-[#006A4C] px-6 pb-18 pt-40 text-[#F1F7F6] sm:pt-34 md:pb-20 md:pt-32">
            <div class="relative z-10 mx-auto max-w-7xl text-center sp-private-plans-hero animate__animated animate__fadeIn" style="--animate-duration: 3s;">
                <h2 class="mx-auto mb-2 max-w-xs text-3xl font-bold leading-tight sm:max-w-4xl sm:text-4xl md:text-5xl">
                    Somos una nueva empresa 
                </h2>
                <h3 class="mx-auto mb-4 max-w-xs text-[#2CC295] text-2xl font-bold leading-tight sm:max-w-2xl sm:text-2xl md:text-4xl">en busca de la excelencia</h3>
                <p class="mx-auto mb-4 max-w-xs text-lg text-[#AAC8C4] sm:max-w-3xl md:text-2xl">
                    Acompañamos tu proceso en publicaciones académicas y científicas
                </p>
            </div>
        </section>
        
        {{--
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
        --}}

        <section class="relative z-20 mx-auto -mt-12 mb-16 w-full max-w-7xl px-6 py-16">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-4xl font-bold text-[#000F1F]">Nuestros servicios</h2>
                <div class="home-service-phrases mx-auto mt-6 max-w-xs sm:max-w-3xl" aria-live="polite">
                    <p class="home-service-phrase text-lg font-semibold leading-8 text-[#000F1F] md:text-xl">
                        En propaps.com, garantizamos excelencia con Inteligencia Híbrida.
                    </p>
                    <p class="home-service-phrase text-lg font-semibold leading-8 text-[#000F1F] md:text-xl">
                        Te apoyamos en todos los momentos de tu proyecto. Inteligencia Híbrida a la medida de tus necesidades.
                    </p>
                    <p class="home-service-phrase text-lg font-semibold leading-8 text-[#000F1F] md:text-xl">
                        Tú defines tus metas, nosotros te acompañamos para alcanzarlas.
                    </p>
                </div>
            </div>

            <div class="plans-carousel">
                <div class="plans-track">
                    @foreach ([false, true] as $isDuplicateSet)
                        <div class="plans-carousel-set" @if ($isDuplicateSet) aria-hidden="true" @endif>
                            @foreach ($homePlanCards as $card)
                                @php
                                    $isFeatured = $card['featured'];
                                    $cardClasses = $isFeatured
                                        ? 'relative border-[#2CC295] bg-white shadow-xl shadow-[#006A4C]/10 ring-1 ring-[#2CC295]/25'
                                        : 'border-[#AAC8C4]/35 bg-white shadow-md';
                                    $buttonClasses = $isFeatured
                                        ? 'bg-[#2CC295] text-[#000F1F] hover:bg-[#00BF81]'
                                        : 'bg-[#006A4C] text-[#F1F7F6] hover:bg-[#00BF81]';
                                @endphp

                                <article class="plan-card-home {{ $cardClasses }} flex h-full flex-col overflow-hidden rounded-2xl border p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
                                    <div class="mb-5 flex min-h-16 flex-wrap items-start justify-between gap-2">
                                        <div class="flex flex-wrap gap-2">
                                            <span class="inline-flex rounded-full bg-[#2CC295]/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#000F1F]">
                                                {{ $card['tag'] }}
                                            </span>
                                            <span class="inline-flex rounded-full bg-[#006A4C]/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#000F1F]">
                                                Inteligencia Híbrida
                                            </span>
                                        </div>

                                        @if (! empty($card['secondary_badge']))
                                            <span class="inline-flex rounded-full {{ $isFeatured ? 'bg-[#2CC295]' : 'bg-[#F1F7F6]' }} px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#000F1F]">
                                                {{ $card['secondary_badge'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mb-5">
                                        <h3 class="mb-3 break-words text-[1.7rem] font-bold leading-tight text-[#000F1F]">{{ $card['name'] }}</h3>
                                        <p class="break-words text-lg leading-8 text-[#000F1F]">{{ $card['description'] }}</p>
                                    </div>

                                    <div class="mb-5 rounded-xl border border-[#AAC8C4]/30 bg-[#F1F7F6] p-4">
                                        <p class="break-words text-[1.65rem] font-bold leading-tight text-[#000F1F]">{{ $card['price'] }}</p>
                                        @if (! empty($card['discount']))
                                            <span class="mt-3 inline-flex rounded-full bg-[#006A4C]/10 px-3 py-1 text-xs font-bold text-[#006A4C]">
                                                {{ $card['discount'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <ul class="mb-6 space-y-3 text-lg leading-8 text-[#000F1F]">
                                        @foreach ($card['benefits'] as $benefit)
                                            <li class="flex gap-3">
                                                <span class="mt-0.5 font-bold text-[#2CC295]">&#10003;</span>
                                                <span>{{ $benefit }}</span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <a href="{{ $plansUrl }}#{{ $card['anchor'] }}-detalle" class="mt-auto block rounded-lg px-5 py-3 text-center font-semibold shadow-sm transition-colors {{ $buttonClasses }}" @if ($isDuplicateSet) tabindex="-1" @endif>
                                        Ver detalle
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    @endforeach
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
                    Unete a cientos de investigadores que confian en propaps.com para llevar su trabajo al siguiente nivel
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
