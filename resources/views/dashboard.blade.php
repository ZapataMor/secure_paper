<x-layouts::app :title="__('Inicio')">
    @php
        $currentUser = auth()->user();
        $isAdmin = $currentUser?->isAdmin() ?? false;
        $isAdvisor = $currentUser?->isAdvisor() ?? false;
        $isClient = $currentUser?->isClient() ?? false;
    @endphp

    <section class="sp-admin-page">
        @if($isAdmin || $isAdvisor)
            <header class="sp-admin-page-header">
                <p class="sp-admin-page-kicker">Panel principal</p>
                <h1>Modulos del sistema</h1>
                <p>Selecciona el modulo que deseas gestionar desde la barra superior o desde las tarjetas.</p>
            </header>

            <div class="sp-admin-modules-grid">
                @if($isAdmin)
                    <article class="sp-admin-module-card">
                        <span class="sp-admin-module-badge">Modulo 01</span>
                        <h2>Gestion de usuarios</h2>
                        <p>Administra la informacion de los usuarios registrados en propaps.com.</p>
                        <a href="{{ route('admin.users.index') }}" class="sp-admin-module-action" wire:navigate>
                            Abrir modulo
                        </a>
                    </article>
                @endif

                <article class="sp-admin-module-card">
                    <span class="sp-admin-module-badge">{{ $isAdmin ? 'Modulo 02' : 'Modulo 01' }}</span>
                    <h2>Proyectos</h2>
                    <p>Consulta los expedientes a tu cargo, revisa documentos y comparte mensajes de seguimiento.</p>
                    <a href="{{ route('admin.works.index') }}" class="sp-admin-module-action" wire:navigate>
                        Abrir modulo
                    </a>
                </article>
            </div>
        @elseif($isClient)
            @php
                $hasActiveMembership = $currentUser?->hasActiveMembership() ?? false;
                $activePlanName = $currentUser?->activeMembershipPlanName();
            @endphp

            <style>
                .sp-start { --sp-mint: #2cc295; --sp-text: #f1f7f6; --sp-muted: #b5cec8; width: min(100%, 1080px); margin: 8px auto 0; color: var(--sp-text); }
                .sp-start *, .sp-start *::before, .sp-start *::after { box-sizing: border-box; }
                .sp-start h1, .sp-start h2, .sp-start h3, .sp-start p { margin: 0; }
                .sp-start a { text-decoration: none; }
                .sp-start a:focus-visible, .sp-start button:focus-visible { outline: 3px solid var(--sp-mint); outline-offset: 5px; }
                .sp-start-kicker { color: var(--sp-mint); font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
                .sp-start-hero { display: grid; grid-template-columns: 1.5fr 1fr; align-items: center; gap: 44px; padding: 14px 0 34px; }
                .sp-start h1 { margin-top: 15px; font-size: clamp(32px, 4vw, 48px); font-weight: 700; line-height: 1.12; letter-spacing: -.035em; }
                .sp-start h1 span { color: var(--sp-mint); }
                .sp-start-intro { max-width: 590px; margin-top: 18px !important; color: var(--sp-muted); font-size: 16px; line-height: 1.7; }
                .sp-start-model { display: flex; flex-wrap: wrap; align-items: center; gap: 7px; margin-top: 22px; color: #d5e8e2; font-size: 12px; }
                .sp-start-model b { color: var(--sp-mint); font-weight: 600; }
                .sp-start-next { padding: 25px; border: 1px solid #2cc29545; border-radius: 18px; background: linear-gradient(140deg, #006a4c30, #001c18a6); }
                .sp-start-state { display: inline-flex; align-items: center; gap: 7px; color: #c5ddd5; font-size: 12px; }
                .sp-start-state::before { content: ''; width: 6px; height: 6px; flex: 0 0 auto; border-radius: 50%; background: #a6c7bc; }
                .sp-start-state.is-active { color: var(--sp-mint); }
                .sp-start-state.is-active::before { background: var(--sp-mint); }
                .sp-start-next h2 { margin-top: 13px; font-size: 21px; font-weight: 650; line-height: 1.3; }
                .sp-start-next p { margin-top: 10px; color: var(--sp-muted); font-size: 14px; line-height: 1.65; overflow-wrap: anywhere; }
                .sp-start-button { display: inline-flex; align-items: center; justify-content: center; gap: 15px; margin-top: 19px; padding: 11px 17px; border: 1px solid transparent; border-radius: 9px; background: var(--sp-mint); color: #00251b; font-size: 13px; font-weight: 700; line-height: 1.4; }
                .sp-start-button:hover { background: #51d3ac; }
                .sp-start-button svg { width: 16px; height: 16px; flex: 0 0 auto; }
                .sp-start-section-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 18px; }
                .sp-start h2 { font-weight: 650; letter-spacing: -.02em; }
                .sp-start-section-head h2 { font-size: 23px; line-height: 1.3; }
                .sp-start-section-head p { margin-top: 7px; color: var(--sp-muted); font-size: 14px; line-height: 1.6; }
                .sp-start-link { color: var(--sp-mint); font-size: 13px; font-weight: 600; }
                .sp-start-link:hover { text-decoration: underline; text-underline-offset: 4px; }
                .sp-start-services { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 15px; }
                .sp-start-service { display: flex; flex-direction: column; padding: 22px; border: 1px solid #2cc2952e; border-radius: 15px; background: #001e1ac2; }
                .sp-start-service-label { color: #a5c3b9; font-size: 10px; font-weight: 650; letter-spacing: .12em; text-transform: uppercase; }
                .sp-start-service h3 { margin-top: 11px; font-size: 19px; font-weight: 650; line-height: 1.3; letter-spacing: -.02em; }
                .sp-start-service p { margin-top: 10px; margin-bottom: 18px; color: var(--sp-muted); font-size: 14px; line-height: 1.65; }
                .sp-start-service .sp-start-link { margin-top: auto; padding-top: 13px; border-top: 1px solid #2cc29520; }
                .sp-start-guide { margin-top: 30px; padding: 27px; border: 1px solid #2cc29530; border-radius: 19px; background: #032a1e55; }
                .sp-start-steps { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; list-style: none; padding: 0; margin: 0; }
                .sp-start-step { display: grid; grid-template-columns: 33px 1fr; gap: 13px; padding: 19px; border: 1px solid #2cc29526; border-radius: 12px; background: #001c18a6; }
                .sp-start-number { display: grid; place-items: center; width: 33px; height: 33px; border: 1px solid #2cc29555; border-radius: 10px; background: #006a4c2e; color: var(--sp-mint); font-size: 12px; font-weight: 700; }
                .sp-start-step h3 { margin-top: 5px; font-size: 16px; font-weight: 650; line-height: 1.35; }
                .sp-start-step p { margin-top: 9px; color: var(--sp-muted); font-size: 13px; line-height: 1.7; }
                .sp-start-upload { display: inline-flex; align-items: center; gap: 8px; margin-top: 13px; padding: 8px 11px; border: 1px solid #2cc29555; border-radius: 8px; background: #006a4c24; color: #d5e8e2; font-size: 12px; font-weight: 600; }
                .sp-start-upload:hover { background: #006a4c55; }
                .sp-start-upload:disabled { cursor: not-allowed; background: #102a23; border-color: #52756666; color: #a5bcb3; }
                .sp-start-upload svg { width: 13px; height: 13px; }
                .sp-start-step .sp-start-access { font-size: 11px; color: #eac583; }
                .sp-start-note { display: grid; grid-template-columns: 30px 1fr; align-items: start; gap: 12px; margin-top: 20px; padding: 20px 0 2px; border-top: 1px solid #2cc29530; }
                .sp-start-note svg { width: 22px; height: 22px; margin-top: 2px; color: var(--sp-mint); }
                .sp-start-note h3 { font-size: 14px; font-weight: 650; }
                .sp-start-note p { margin-top: 6px; color: var(--sp-muted); font-size: 13px; line-height: 1.65; }
                .sp-start-footer { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px 20px; margin-top: 20px; padding-bottom: 8px; color: #a5c3b9; font-size: 12px; line-height: 1.6; }
                @media (max-width: 800px) {
                    .sp-start-hero { grid-template-columns: 1fr; gap: 24px; }
                    .sp-start-next { padding: 22px; }
                    .sp-start-services { gap: 11px; }
                    .sp-start-service { padding: 18px; }
                }
                @media (max-width: 600px) {
                    .sp-start { margin-top: 0; }
                    .sp-start-hero { padding-top: 9px; padding-bottom: 28px; }
                    .sp-start-intro { font-size: 15px; }
                    .sp-start-services, .sp-start-steps { grid-template-columns: 1fr; }
                    .sp-start-section-head { align-items: start; flex-direction: column; gap: 11px; }
                    .sp-start-guide { margin-top: 23px; padding: 19px 15px; }
                    .sp-start-step { padding: 16px 13px; gap: 11px; }
                    .sp-start-service { padding: 20px; }
                    .sp-start-footer { flex-direction: column; }
                }
            </style>

            <article class="sp-start" aria-labelledby="sp-start-title">
                <header class="sp-start-hero">
                    <div>
                        <p class="sp-start-kicker">Tu espacio de trabajo</p>
                        <h1 id="sp-start-title">Bienvenido a<br><span>propaps.com</span></h1>
                        <p class="sp-start-intro">Acompañamos la escritura, corrección y preparación de tu manuscrito académico o científico en ciencias sociales y educación. Tu conocimiento, el criterio del redactor y el apoyo de la IA se integran en cada etapa.</p>
                        <div class="sp-start-model" aria-label="Modelo de Inteligencia Híbrida">
                            <span>Inteligencia Humana</span><b aria-hidden="true">+</b><span>Inteligencia Artificial</span><b aria-hidden="true">=</b><b>Inteligencia Híbrida</b>
                        </div>
                    </div>
                    @include('partials.client-journey')
                </header>

                @if(!$hasActiveMembership)
                <section aria-labelledby="sp-start-services-title">
                    <div class="sp-start-section-head">
                        <div>
                            <h2 id="sp-start-services-title">Un servicio para cada manuscrito</h2>
                            <p>Conoce las tres líneas de nuestro portafolio.</p>
                        </div>
                        <a href="{{ route('plans') }}" class="sp-start-link">Ver portafolio completo →</a>
                    </div>
                    <div class="sp-start-services">
                        <article class="sp-start-service">
                            <span class="sp-start-service-label">Manuscritos avanzados</span>
                            <h3>Corrección Completa</h3>
                            <p>Ortografía, estilo, coherencia y argumentación. Formato APA 7 o el exigido por tu universidad, con ajustes post-revisión.</p>
                            <a href="{{ route('private.planes', ['paquete' => 'correccion-completa']) }}" class="sp-start-link" wire:navigate>Consultar este servicio →</a>
                        </article>
                        <article class="sp-start-service">
                            <span class="sp-start-service-label">Publicaciones científicas</span>
                            <h3>Modo Indexación</h3>
                            <p>Redacción y optimización del artículo, selección estratégica de revista y acompañamiento editorial durante un año.</p>
                            <a href="{{ route('private.planes', ['paquete' => 'modo-indexacion']) }}" class="sp-start-link" wire:navigate>Consultar este servicio →</a>
                        </article>
                        <article class="sp-start-service">
                            <span class="sp-start-service-label">Libros académicos y científicos</span>
                            <h3>Grandes Volúmenes</h3>
                            <p>Redacción tripartita, estructura académica y optimización integral. Versión editada lista para maquetación y envío a la editorial.</p>
                            <a href="{{ route('private.planes', ['paquete' => 'grandes-volumenes']) }}" class="sp-start-link" wire:navigate>Consultar este servicio →</a>
                        </article>
                    </div>
                </section>
                @endif

                <section class="sp-start-guide" aria-labelledby="sp-start-guide-title">
                    <div class="sp-start-section-head">
                        <div>
                            <h2 id="sp-start-guide-title">Cómo avanzar con tu proyecto</h2>
                            <p>Una ruta compartida entre autor, redactor e inteligencia artificial.</p>
                        </div>
                    </div>
                    <ol class="sp-start-steps">
                        <li class="sp-start-step">
                            <span class="sp-start-number" aria-hidden="true">01</span>
                            <div>
                                <h3>Contratación y preparación</h3>
                                <p>Elige el paquete en Planes, acepta sus condiciones y realiza el pago total. Con el servicio activo, carga tu manuscrito y las indicaciones. Por mensajería acordamos la sesión inicial y su preparación.</p>
                                @if($hasActiveMembership)
                                    <a href="{{ route('private.upload-document') }}" class="sp-start-upload" wire:navigate>Cargar documentos →</a>
                                @else
                                    <button type="button" class="sp-start-upload" disabled aria-describedby="sp-start-access-note">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2" /><path d="M8 10V7a4 4 0 0 1 8 0v3" /></svg>
                                        Cargar documentos
                                    </button>
                                    <p id="sp-start-access-note" class="sp-start-access">Disponible con un servicio activo y pagado.</p>
                                @endif
                            </div>
                        </li>
                        <li class="sp-start-step">
                            <span class="sp-start-number" aria-hidden="true">02</span>
                            <div>
                                <h3>Diálogo y orientación</h3>
                                <p>En la sesión inicial revisamos contigo las ideas centrales, los objetivos, la metodología y el avance del documento. Definimos cómo organizar el trabajo según el servicio contratado.</p>
                            </div>
                        </li>
                        <li class="sp-start-step">
                            <span class="sp-start-number" aria-hidden="true">03</span>
                            <div>
                                <h3>Desarrollo del manuscrito</h3>
                                <p>Trabajamos con tus insumos mediante Inteligencia Híbrida. Recibes un primer borrador o una versión intervenida, junto con la descripción del resultado o de la propuesta.</p>
                            </div>
                        </li>
                        <li class="sp-start-step">
                            <span class="sp-start-number" aria-hidden="true">04</span>
                            <div>
                                <h3>Ajustes y entrega</h3>
                                <p>Comparte tus comentarios para realizar ajustes progresivos mediante intercambios asincrónicos. Entregamos la versión final de acuerdo con tus necesidades y los plazos acordados.</p>
                            </div>
                        </li>
                    </ol>
                    <aside class="sp-start-note" aria-labelledby="sp-start-scope-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 4 6v6c0 5 8 9 8 9s8-4 8-9V6l-8-3Z" /><path d="m8 12 3 3 5-6" /></svg>
                        <div>
                            <h3>El acompañamiento corresponde al paquete contratado</h3>
                            <p>Los plazos y entregables se detallan en Planes. Modo Indexación incluye un año de acompañamiento editorial, con adaptación y envío a nuevas revistas cuando sea necesario.</p>
                        </div>
                    </aside>
                </section>
                <footer class="sp-start-footer">
                    <span>Tu participación y los insumos que aportas orientan cada etapa.</span>
                    <a href="{{ route('about') }}" class="sp-start-link">Conoce nuestro modelo de trabajo →</a>
                </footer>
            </article>

        @endif
    </section>
</x-layouts::app>
