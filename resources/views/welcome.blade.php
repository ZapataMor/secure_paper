<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Propaps.com · Escritura y acompañamiento académico</title>
        <meta name="description" content="Escritura, corrección y preparación de manuscritos académicos y científicos con Inteligencia Híbrida. Conoce los servicios de Propaps.com.">

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

            .sp-home-accompaniment { font-size: calc(1.125rem + 2pt); }
            @media (min-width: 768px) {
                .sp-home-accompaniment { font-size: calc(1.5rem + 2pt); }
            }

            .sp-service-carousel { overflow: hidden; position: relative; max-width: 1084px; margin: 0 auto; padding: 8px 0 18px; }
            .sp-service-track { display: flex; width: max-content; animation: sp-service-scroll 42s linear 7s infinite; }
            .sp-service-set { display: flex; align-items: stretch; gap: 20px; padding-right: 20px; }
            .sp-service-carousel:hover .sp-service-track,
            .sp-service-carousel:focus-within .sp-service-track,
            .sp-service-carousel[data-paused="true"] .sp-service-track { animation-play-state: paused; }
            .sp-service-card { display: flex; flex-direction: column; flex: 0 0 348px; width: 348px; padding: 24px;
                border: 1px solid #d5e4e0; border-radius: 18px; background: #fff;
                box-shadow: 0 6px 18px #000f1f08; color: #000f1f; }
            .sp-service-card__top { display: flex; align-items: center; gap: 12px; margin-bottom: 19px; }
            .sp-service-icon { display: grid; place-items: center; width: 42px; height: 42px; flex-shrink: 0;
                border-radius: 12px; background: #edf7f3; color: #006a4c; }
            .sp-service-icon svg { width: 23px; height: 23px; }
            .sp-service-tag { font-size: 12px; font-weight: 600; letter-spacing: .035em; color: #006a4c; }
            .sp-service-card h3 { margin: 0 0 10px; font-size: 23px; font-weight: 650; line-height: 1.25; letter-spacing: -.025em; }
            .sp-service-description { min-height: 66px; margin-bottom: 18px; font-size: 14px; line-height: 1.6; color: #425c60; }
            .sp-service-benefits { display: grid; gap: 9px; padding: 0; margin: 0 0 22px; list-style: none; font-size: 13px; line-height: 1.5; }
            .sp-service-benefits li { display: flex; align-items: flex-start; gap: 9px; }
            .sp-service-check { color: #00815d; font-weight: 700; }
            .sp-service-price { border-top: 1px solid #e2ece8; padding-top: 17px; margin-top: auto; }
            .sp-service-amount { font-size: 28px; line-height: 1.2; font-weight: 650; letter-spacing: -.035em; }
            .sp-service-price-prefix { font-size: 12px; color: #425c60; margin-bottom: 3px; min-height: 18px; }
            .sp-service-currency { font-size: 12px; font-weight: 500; letter-spacing: 0; color: #425c60; }
            .sp-service-price-note { font-size: 11px; line-height: 1.55; color: #536b70; margin: 7px 0 17px; min-height: 34px; }
            .sp-service-cta { display: flex; align-items: center; justify-content: space-between; padding: 11px 14px;
                border: 1px solid #c9ded6; border-radius: 10px; background: #f2f8f5; color: #006a4c;
                font-size: 13px; font-weight: 650; text-decoration: none; transition: background .2s; }
            .sp-service-cta:hover { background: #e2f2e9; }
            .sp-service-cta:focus-visible, .sp-service-pause:focus-visible { outline: 2px solid #006a4c; outline-offset: 4px; }
            .sp-service-pause { display: block; margin: 0 auto; padding: 7px 12px; border: 1px solid #c9ded6;
                border-radius: 20px; color: #425c60; font-size: 12px; background: transparent; cursor: pointer; }
            .sp-service-footnote { text-align: center; color: #536b70; font-size: 12px; margin: 18px 0 0; }
            .sp-footer-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
            .sp-footer-contact { justify-self: end; }
            @keyframes sp-service-scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
            @media (max-width: 640px) {
                .sp-service-card { flex-basis: min(82vw, 328px); width: min(82vw, 328px); padding: 21px; }
                .sp-service-card h3 { font-size: 21px; }
                .sp-service-set { gap: 16px; padding-right: 16px; }
                .sp-footer-grid { grid-template-columns: 1fr; gap: 28px; }
                .sp-footer-contact { justify-self: start; }
            }
            @media (prefers-reduced-motion: reduce) {
                .sp-service-carousel { overflow-x: auto; }
                .sp-service-track { animation: none; }
                .sp-service-set[aria-hidden="true"] { display: none; }
                .sp-service-pause { display: none; }
            }

            /* Versión fija: todos los paquetes disponibles para comparar. */
            .sp-service-carousel { overflow: visible; max-width: 1060px; padding: 6px 0; }
            .sp-service-track { display: block; width: 100%; animation: none; }
            .sp-service-set { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 18px; padding: 0; }
            .sp-service-card { width: auto; flex: none; padding: 22px; }
            .sp-service-description { min-height: 68px; margin-bottom: 14px; }
            .sp-service-benefits { gap: 8px; margin-bottom: 18px; }
            .sp-service-price-note { margin-bottom: 14px; }
            .sp-home-services, .sp-home-comments { max-width: 1108px; margin-left: auto; margin-right: auto; padding: 28px 24px; }
            .sp-home-services { margin-top: 0; margin-bottom: 0; }
            .sp-home-services > div:first-child { margin-bottom: 22px; }
            .sp-home-services h2 { font-size: 30px; margin-bottom: 10px; }
            .sp-home-services .home-service-phrases { min-height: 52px; margin-top: 0; }
            .sp-home-services .home-service-phrase { font-size: 16px; line-height: 1.65; }
            .sp-home-comments > div:first-child { margin-bottom: 20px; }
            .sp-home-comments > div:first-child h2 { font-size: 28px; margin-bottom: 10px; }
            .sp-home-comments > div:first-child p { font-size: 16px; line-height: 1.65; }
            .sp-home-comments > div:last-child { padding: 22px; gap: 24px; }
            .sp-home-comments textarea { min-height: 96px; }
            .sp-home-comments button[type="submit"] { margin-top: 16px; }
            .sp-home-hero-grid-bg { padding-top: 120px; padding-bottom: 34px; }
            .sp-public-navbar-inner { max-width: 1060px; }
            .sp-service-footnote { font-size: 15px; line-height: 1.65; margin-top: 22px; color: #006a4c; font-weight: 500; }
            .sp-reference-link { font-size: 11px; padding: 0 3px; text-decoration: underline; text-underline-offset: 2px; }
            .sp-reference-link:focus-visible { outline: 2px solid #006a4c; outline-offset: 3px; }
            @media (max-width: 960px) {
                .sp-service-set { grid-template-columns: minmax(0,1fr); max-width: 560px; margin: 0 auto; gap: 16px; }
                .sp-service-description { min-height: 0; }
                .sp-service-price-note { min-height: 0; }
                .sp-service-card { padding: 22px; }
                .sp-service-card__top { margin-bottom: 12px; }
                .sp-service-card h3 { font-size: 22px; }
            }
            @media (max-width: 640px) {
                .sp-home-services, .sp-home-comments { padding: 24px 20px; }
                .sp-home-hero-grid-bg { padding-top: 160px; padding-bottom: 26px; }
                .sp-home-comments > div:last-child { padding: 20px; }
                .sp-service-footnote { font-size: 14px; }
            }
            @media (prefers-reduced-motion: reduce) {
                .sp-service-carousel { overflow: visible; }
            }

            .sp-home-services .home-service-phrase { animation: none; opacity: 1; transform: none; }
            .sp-home-services .home-service-phrase:not(:first-child) { display: none; }

            #concepto-inteligencia-hibrida { scroll-margin-top: 120px; }
            #concepto-inteligencia-hibrida:target { background: #e4f2ec; border-radius: 8px; }
            #concepto-inteligencia-hibrida:focus-visible { outline: 2px solid #006a4c; outline-offset: 5px; }
            @media (max-width: 640px) {
                #concepto-inteligencia-hibrida { scroll-margin-top: 180px; }
            }
        </style>
    </head>
    <body class="min-h-screen overflow-x-hidden bg-[#F1F7F6] text-[17px]">
        @php
            $loginUrl = Route::has('login') ? route('login') : '#';
            $plansUrl = Route::has('plans') ? route('plans') : url('/planes');
            $homePlanCards = [
                [
                    'name' => 'Corrección Completa',
                    'tag' => 'Manuscritos académicos',
                    'icon' => 'document',
                    'description' => 'Pulimos tu manuscrito para que tus ideas expresen claridad y rigor académico.',
                    'benefits' => ['Ortografía, estilo y argumentación', 'Formato APA 7 o el de tu universidad', 'Feedback abierto para optimización de resultados'],
                    'price' => '550',
                    'prefix' => '',
                    'note' => 'Hasta 40.000 palabras por documento.',
                    'detail' => 'Por cada bloque adicional de hasta 2.000 palabras: 50 USD + IVA.',
                ],
                [
                    'name' => 'Modo Indexación',
                    'tag' => 'Publicaciones científicas',
                    'icon' => 'journal',
                    'description' => 'Preparamos tu artículo y te acompañamos en el proceso de publicación científica.',
                    'benefits' => ['Selección estratégica de revista', 'Redacción y optimización del artículo', 'Un año de acompañamiento editorial'],
                    'price' => '510',
                    'prefix' => 'Desde',
                    'note' => 'Pregrado: 510 · Maestría: 540 · Doctorado: 570 USD.',
                    'detail' => 'El acompañamiento incluye adaptación y envío a nuevas revistas si es necesario.',
                ],
                [
                    'name' => 'Grandes Volúmenes',
                    'tag' => 'Libros académicos',
                    'icon' => 'book',
                    'description' => 'Damos estructura y calidad editorial a tu libro científico o académico.',
                    'benefits' => ['Redacción con Inteligencia Híbrida', 'Estructura y optimización integral', 'Versión lista para maquetación y envío'],
                    'price' => '650',
                    'prefix' => 'Desde',
                    'note' => 'Desde 100 páginas por documento.',
                    'detail' => 'De 100 a 200 páginas: 720 USD + IVA. Más de 200 páginas: 800 USD + IVA.',
                ],
            ];
        @endphp

        @include('partials.public.header')

        <main id="contenido-principal">

        <section class="sp-home-hero-grid-bg relative z-0 bg-gradient-to-br from-[#000F1F] via-[#032221] to-[#006A4C] px-6 pb-18 pt-40 text-[#F1F7F6] sm:pt-34 md:pb-20 md:pt-32">
            <div class="relative z-10 mx-auto max-w-7xl text-center sp-private-plans-hero animate__animated animate__fadeIn" style="--animate-duration: 3s;">
                <h2 class="sp-home-accompaniment mx-auto mb-4 max-w-xs font-normal text-[#AAC8C4] sm:max-w-3xl">
                    Acompañamos tu proceso de publicación académica y científica en ciencias sociales
                </h2>
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

        <section class="sp-home-services relative z-20" id="servicios">
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

            <div class="sp-service-carousel" id="servicios-carrusel" aria-label="Paquetes de servicios">
                <div class="sp-service-track">
                        <div class="sp-service-set">
                            @foreach ($homePlanCards as $card)
                                
                                <article class="sp-service-card">
                                    <div class="sp-service-card__top">
                                        <span class="sp-service-icon">
                                            @if ($card['icon'] === 'document')
                                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 12h8M8 16h5"/></svg>
                                            @elseif ($card['icon'] === 'journal')
                                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h3M8 18h8M15 15h1"/></svg>
                                            @else
                                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6c-3-2-6-2-10-1v15c4-1 7-1 10 1 3-2 6-2 10-1V5c-4-1-7-1-10 1ZM12 6v15"/></svg>
                                            @endif
                                        </span>
                                        <span class="sp-service-tag">{{ $card['tag'] }}</span>
                                    </div>
                                    <h3>{{ $card['name'] }}</h3>
                                    <p class="sp-service-description">{{ $card['description'] }}</p>
                                    <ul class="sp-service-benefits">
                                        @foreach ($card['benefits'] as $benefit)
                                            <li><span class="sp-service-check" aria-hidden="true">✓</span><span>{{ $benefit }}</span></li>
                                        @endforeach
                                    </ul>
                                    <div class="sp-service-price">
                                        <p class="sp-service-price-prefix">{{ $card['prefix'] ?: 'Precio por documento' }}</p>
                                        <p class="sp-service-amount">{{ $card['price'] }} <span class="sp-service-currency">USD + IVA</span></p>
                                        <p class="sp-service-price-note">{{ $card['note'] }}</p>
                                    </div>
                                    <a class="sp-service-cta" href="https://wa.me/573122851357?text={{ rawurlencode('Hola, me interesa el paquete ' . $card['name'] . '. Quisiera conocer más información.') }}" target="_blank" rel="noopener noreferrer" aria-label="Consultar el paquete {{ $card['name'] }} por WhatsApp">
                                        Consultar este paquete <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="width:16px;height:16px"><path d="M7 17 17 7M7 7h10v10"/></svg>
                                    </a>
                                </article>

                            @endforeach
                        </div>
                </div>
            </div>
            <p class="sp-service-footnote" id="concepto-inteligencia-hibrida" tabindex="-1">Inteligencia Humana + Inteligencia Artificial = Inteligencia Híbrida<sup><a class="sp-reference-link" href="#nota-inteligencia-hibrida" aria-label="Leer referencia académica de inteligencia híbrida">1</a></sup></p>

        </section>

        <section class="sp-home-comments" id="comentarios">
            <div class="mb-10 text-center">
                <h2 class="mb-4 text-4xl font-bold text-[#000F1F]">Comentarios sobre nuestro proyecto</h2>
                <p class="mx-auto max-w-3xl text-lg leading-8 text-[#000F1F]">
                    Nos interesa lo que piensas. Opina sobre nuestro proyecto. Inteligencia Híbrida al servicio de la ciencia y el conocimiento.
                </p>
            </div>

            <div class="mx-auto grid max-w-5xl gap-8 rounded-2xl border border-[#006A4C]/15 bg-white p-6 shadow-lg md:grid-cols-[0.8fr_1.2fr] md:p-8">
                <div>
                    <span class="mb-4 inline-flex rounded-full bg-[#2CC295]/15 px-4 py-1.5 text-sm font-bold uppercase tracking-wide text-[#000F1F]">
                        Registro de opiniones
                    </span>
                    <h3 class="mb-4 text-2xl font-bold leading-tight text-[#000F1F]">Tu voz ayuda a mejorar propaps.com</h3>
                    <p class="text-base leading-7 text-[#000F1F]">
                        Los comentarios se revisan antes de publicarse. Las opiniones honestas y respetuosas podran aparecer en la pagina cuando el proyecto cuente con clientes y registros validados.
                    </p>
                </div>

                <form action="mailto:comunicaciones@propaps.com" method="POST" enctype="text/plain" class="grid gap-4">
                    <label class="grid gap-2 text-sm font-semibold text-[#000F1F]">
                        Nombre
                        <input type="text" name="nombre" required class="rounded-xl border border-[#AAC8C4] px-4 py-3 text-base font-normal text-[#000F1F] outline-none transition focus:border-[#2CC295] focus:ring-2 focus:ring-[#2CC295]/30">
                    </label>
                    <label class="grid gap-2 text-sm font-semibold text-[#000F1F]">
                        Correo electronico
                        <input type="email" name="correo" required class="rounded-xl border border-[#AAC8C4] px-4 py-3 text-base font-normal text-[#000F1F] outline-none transition focus:border-[#2CC295] focus:ring-2 focus:ring-[#2CC295]/30">
                    </label>
                    <label class="grid gap-2 text-sm font-semibold text-[#000F1F]">
                        Comentario
                        <textarea name="comentario" rows="3" required class="resize-y rounded-xl border border-[#AAC8C4] px-4 py-3 text-base font-normal text-[#000F1F] outline-none transition focus:border-[#2CC295] focus:ring-2 focus:ring-[#2CC295]/30"></textarea>
                    </label>
                    <button type="submit" class="rounded-xl bg-[#006A4C] px-6 py-3 text-base font-bold text-[#F1F7F6] transition-colors hover:bg-[#00BF81]">
                        Enviar opinion
                    </button>
                </form>
            </div>
        </section>

        </main>

        @include('partials.public.footer')

        
    </body>
</html>
