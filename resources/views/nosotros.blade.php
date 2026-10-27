<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nosotros — PROPAPS</title>
        <meta name="description" content="PROPAPS acompaña la producción de conocimiento científico en ciencias sociales con un modelo de Inteligencia Híbrida que integra autor, redactor e inteligencia artificial.">
        <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('assets/images/logo.png') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .sp-about-page { background: #f1f7f6; color: #000f1f; }
            .sp-about-page *, .sp-about-page *::before, .sp-about-page *::after { box-sizing: border-box; }
            .sp-about-wrap { width: min(1060px, calc(100% - 48px)); margin-inline: auto; }
            .sp-about-page h1, .sp-about-page h2, .sp-about-page h3, .sp-about-page p { margin: 0; }
            .sp-about-page p { font-size: 16px; line-height: 1.75; }
            .sp-about-page h2 { font-size: clamp(25px, 3vw, 33px); font-weight: 700; line-height: 1.25; letter-spacing: -.025em; }
            .sp-about-page h3 { font-size: 20px; font-weight: 650; line-height: 1.3; }
            .sp-about-eyebrow { display: block; margin-bottom: 14px; color: #006a4c; font-size: 12px; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; }
            .sp-about-hero { padding: 148px 0 44px; background: linear-gradient(120deg, #000f1f 15%, #032221 75%, #064d3a); color: #f1f7f6; }
            .sp-about-hero .sp-about-eyebrow { color: #2cc295; }
            .sp-about-hero h1 { max-width: 820px; font-size: clamp(32px, 4.8vw, 50px); font-weight: 700; line-height: 1.12; letter-spacing: -.035em; }
            .sp-about-hero p { max-width: 820px; margin-top: 22px; color: #c5dbd7; font-size: 18px; line-height: 1.7; }
            .sp-about-intent { display: flex; flex-wrap: wrap; gap: 12px 25px; margin-top: 25px; padding-top: 21px; border-top: 1px solid #2cc29540; color: #d7ebe6; font-size: 13px; }
            .sp-about-intent span::before { content: ""; display: inline-block; width: 5px; height: 5px; margin: 0 9px 2px 0; border-radius: 50%; background: #2cc295; }
            .sp-about-purpose { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; padding-block: 43px; }
            .sp-about-purpose p { color: #405b58; }
            .sp-about-purpose p + p { margin-top: 14px; }
            .sp-about-purpose h2 { max-width: 420px; }
            .sp-about-model { background: #fff; padding-block: 39px; border-block: 1px solid #d8e7e3; }
            .sp-about-section-intro { max-width: 790px; margin-top: 14px !important; color: #405b58; }
            .sp-about-roles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 17px; margin-top: 25px; }
            .sp-about-role { padding: 22px; border: 1px solid #d8e7e3; border-radius: 16px; background: #f8fbfa; }
            .sp-about-role-mark { display: inline-block; margin-bottom: 15px; color: #006a4c; font-size: 12px; font-weight: 700; letter-spacing: .08em; }
            .sp-about-role h3 { margin-bottom: 11px; }
            .sp-about-role p { color: #405b58; font-size: 15px; line-height: 1.65; }
            .sp-about-model-note { margin-top: 19px !important; color: #405b58; font-size: 14px !important; }
            .sp-about-model-note a { color: #006a4c; text-decoration: underline; text-underline-offset: 3px; }
            .sp-about-process { padding-block: 43px; }
            .sp-about-steps { display: grid; grid-template-columns: repeat(2, 1fr); gap: 17px; margin: 25px 0 0; padding: 0; list-style: none; }
            .sp-about-step { display: grid; grid-template-columns: 34px 1fr; align-items: start; gap: 14px; padding: 22px; border: 1px solid #d2e3dd; border-radius: 16px; background: #fff; }
            .sp-about-step-number { display: grid; place-items: center; width: 34px; height: 34px; border-radius: 50%; background: #006a4c; color: #fff; font-size: 14px; font-weight: 700; }
            .sp-about-step h3 { margin-bottom: 9px; font-size: 18px; }
            .sp-about-step p { color: #405b58; font-size: 15px; line-height: 1.65; }
            .sp-about-time-note { margin-top: 19px !important; color: #405b58; font-size: 14px !important; }
            .sp-about-next { display: flex; align-items: center; justify-content: space-between; gap: 25px; margin-top: 29px; padding-top: 25px; border-top: 1px solid #c9ddd5; }
            .sp-about-next h3 { font-size: 19px; }
            .sp-about-next p { margin-top: 7px; color: #405b58; font-size: 14px; }
            .sp-about-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 12px 19px; }
            .sp-about-primary { display: inline-block; padding: 12px 20px; border-radius: 10px; background: #006a4c; color: #fff; font-size: 14px; font-weight: 700; text-decoration: none; white-space: nowrap; }
            .sp-about-primary:hover { background: #04543e; }
            .sp-about-secondary { color: #006a4c; font-size: 14px; font-weight: 650; text-decoration: underline; text-underline-offset: 4px; }
            .sp-about-page a:focus-visible { outline: 3px solid #00bf81; outline-offset: 4px; }
            .sp-about-model, .sp-about-process { scroll-margin-top: 125px; }
            @media (max-width: 760px) {
                .sp-about-purpose { grid-template-columns: 1fr; gap: 19px; }
                .sp-about-roles { grid-template-columns: 1fr; gap: 12px; }
                .sp-about-next { align-items: flex-start; flex-direction: column; gap: 19px; }
            }
            @media (max-width: 540px) {
                .sp-about-wrap { width: calc(100% - 36px); }
                .sp-about-hero { padding-top: 172px; padding-bottom: 32px; }
                .sp-about-hero p { font-size: 16px; margin-top: 18px; }
                .sp-about-intent { gap: 10px 17px; font-size: 12px; }
                .sp-about-purpose, .sp-about-model, .sp-about-process { padding-block: 31px; }
                .sp-about-steps { grid-template-columns: 1fr; gap: 12px; }
                .sp-about-role, .sp-about-step { padding: 19px; }
            }
        </style>
    </head>
    <body class="min-h-screen bg-[#F1F7F6]">
        @include('partials.public.header')

        <main class="sp-about-page" id="nosotros">
            <section class="sp-about-hero" aria-labelledby="about-title">
                <div class="sp-about-wrap">
                    <span class="sp-about-eyebrow">Nosotros · PROPAPS S.A.S.</span>
                    <h1 id="about-title">Inteligencia Híbrida al servicio del conocimiento</h1>
                    <p>En propaps.com acompañamos la escritura, optimización y preparación de manuscritos académicos y científicos en ciencias sociales. Integramos la participación del autor, la redacción humana y la inteligencia artificial para apoyar la producción de conocimiento.</p>
                    <div class="sp-about-intent" aria-label="Propósitos del servicio">
                        <span>Calidad académica</span>
                        <span>Atención personalizada</span>
                        <span>Propósito de excelencia</span>
                    </div>
                </div>
            </section>

            <section class="sp-about-wrap sp-about-purpose" aria-labelledby="purpose-title">
                <div>
                    <span class="sp-about-eyebrow">Nuestro propósito</span>
                    <h2 id="purpose-title">Apoyar las ideas que contribuyen al conocimiento científico</h2>
                </div>
                <div>
                    <p>Queremos aportar a que estudiantes, investigadores y docentes expresen sus ideas con claridad argumentativa, coherencia y rigor académico. La búsqueda de excelencia orienta la preparación de cada manuscrito y el cuidado de sus objetivos, estructura y contenido.</p>
                    <p>Trabajamos con atención personalizada, confidencialidad y plazos acordados. El diálogo con el autor y la calidad de los insumos que aporta son parte esencial de este proceso.</p>
                </div>
            </section>

            <section class="sp-about-model" id="modelo-tripartito" aria-labelledby="model-title">
                <div class="sp-about-wrap">
                    <span class="sp-about-eyebrow">Nuestro modelo de trabajo</span>
                    <h2 id="model-title">Una producción tripartita</h2>
                    <p class="sp-about-section-intro">Nuestra propuesta innovadora combina autor, redactor e inteligencia artificial. El conocimiento del autor se articula con el criterio de la redacción humana y el apoyo de la IA para construir y optimizar el manuscrito.</p>
                    <div class="sp-about-roles">
                        <article class="sp-about-role">
                            <span class="sp-about-role-mark" aria-hidden="true">01 · CONOCIMIENTO</span>
                            <h3>Autor</h3>
                            <p>Aporta las ideas, los objetivos, los datos y los insumos de su investigación. Participa en el diálogo y orienta el sentido del documento.</p>
                        </article>
                        <article class="sp-about-role">
                            <span class="sp-about-role-mark" aria-hidden="true">02 · CRITERIO HUMANO</span>
                            <h3>Redactor</h3>
                            <p>Da estructura al manuscrito y trabaja en su redacción, coherencia y argumentación, en diálogo con las propuestas del autor.</p>
                        </article>
                        <article class="sp-about-role">
                            <span class="sp-about-role-mark" aria-hidden="true">03 · APOYO TECNOLÓGICO</span>
                            <h3>Inteligencia artificial</h3>
                            <p>Asiste en la organización, redacción y optimización del texto. Sus propuestas se revisan con criterio humano y se contrastan con los insumos del autor.</p>
                        </article>
                    </div>
                    <p class="sp-about-model-note">Inteligencia Humana + Inteligencia Artificial = Inteligencia Híbrida.<sup><a href="#nota-inteligencia-hibrida" aria-label="Leer la referencia académica sobre inteligencia híbrida">1</a></sup> El diálogo y la revisión humana orientan el resultado.</p>
                </div>
            </section>

            <section class="sp-about-wrap sp-about-process" id="como-trabajamos" aria-labelledby="process-title">
                <span class="sp-about-eyebrow">Metodología general</span>
                <h2 id="process-title">Cómo trabajamos</h2>
                <p class="sp-about-section-intro">Combinamos sesiones sincrónicas e intercambios asincrónicos para comprender tu proyecto, desarrollar el manuscrito y realizar ajustes progresivos.</p>
                <ol class="sp-about-steps">
                    <li class="sp-about-step">
                        <span class="sp-about-step-number" aria-hidden="true">1</span>
                        <div>
                            <h3>Contratación y preparación</h3>
                            <p>Eliges el servicio, aceptas sus condiciones y realizas el pago del paquete. Acordamos por mensajería la fecha y hora de la sesión inicial y compartimos recomendaciones para prepararla.</p>
                        </div>
                    </li>
                    <li class="sp-about-step">
                        <span class="sp-about-step-number" aria-hidden="true">2</span>
                        <div>
                            <h3>Diálogo y orientación</h3>
                            <p>En la sesión inicial escuchamos tus propuestas y revisamos el avance del documento, sus ideas centrales, objetivos, metodología y conclusiones. Definimos cómo dar orden al manuscrito según el servicio contratado.</p>
                        </div>
                    </li>
                    <li class="sp-about-step">
                        <span class="sp-about-step-number" aria-hidden="true">3</span>
                        <div>
                            <h3>Desarrollo del manuscrito</h3>
                            <p>Trabajamos sobre tus insumos mediante el modelo tripartito. Recibes un primer borrador o una versión intervenida, acompañado de una descripción del resultado o de la propuesta.</p>
                        </div>
                    </li>
                    <li class="sp-about-step">
                        <span class="sp-about-step-number" aria-hidden="true">4</span>
                        <div>
                            <h3>Ajustes y entrega</h3>
                            <p>Recibimos tus comentarios y realizamos ajustes progresivos mediante intercambios asincrónicos. Entregamos la versión final de acuerdo con tus necesidades y los plazos acordados.</p>
                        </div>
                    </li>
                </ol>
                <p class="sp-about-time-note">Los plazos y el alcance de cada etapa se especifican en el paquete contratado. En Modo Indexación, el acompañamiento editorial continúa durante un año.</p>
                <div class="sp-about-next">
                    <div>
                        <h3>Conoce el servicio que se ajusta a tu proyecto</h3>
                        <p>Consulta las inclusiones, fases y condiciones de cada paquete.</p>
                    </div>
                    <div class="sp-about-actions">
                        <a class="sp-about-primary" href="{{ route('plans') }}">Ver portafolio</a>
                        <a class="sp-about-secondary" href="https://wa.me/573122851357" target="_blank" rel="noopener noreferrer">Conversemos por WhatsApp</a>
                    </div>
                </div>
            </section>
        </main>

        @include('partials.public.footer')
    </body>
</html>
