<style>
    .sp-footer-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
    .sp-footer-contact { justify-self: end; }
    @media (max-width: 640px) {
        .sp-footer-grid { grid-template-columns: 1fr; gap: 28px; }
        .sp-footer-contact { justify-self: start; }
    }

    .sp-compact-footer { padding: 30px 24px 24px; }
    .sp-compact-footer > div { max-width: 1060px; }
    .sp-compact-footer .sp-footer-grid { margin-bottom: 22px; }
    .sp-compact-footer .sp-footer-contact { justify-self: end; }
    .sp-academic-note { font-size: 11px; line-height: 1.7; color: #aac8c4; border-top: 1px solid #006a4c; padding-top: 16px; margin-bottom: 18px; scroll-margin-top: 120px; }
    .sp-academic-note p { margin: 0 0 6px; }
    .sp-academic-note a { color: #cce9df; text-decoration: underline; text-underline-offset: 3px; }
    .sp-academic-note a:focus-visible { outline: 2px solid #2cc295; outline-offset: 3px; }
    .sp-note-number { font-weight: 700; }
    .sp-compact-footer .sp-footer-copyright { border: 0; padding-top: 0; font-size: 11px; }
    @media (max-width: 640px) {
        .sp-compact-footer { padding: 26px 20px 22px; }
        .sp-compact-footer .sp-footer-contact { justify-self: start; }
    }
</style>
<footer class="sp-compact-footer bg-[#000F1F] px-6 py-12 text-[#F1F7F6]">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 sp-footer-grid">
            <div>
                <h4 class="mb-4 font-semibold">Legal</h4>
                <ul class="space-y-2 text-sm text-[#AAC8C4]">
                    <li>
                        <a class="sp-public-footer-link" href="{{ route('legal.privacy') }}">Política de tratamiento de datos</a>
                    </li>
                    <li>
                        <a class="sp-public-footer-link" href="{{ route('legal.terms') }}">Términos y condiciones</a>
                    </li>
                    <li><a class="sp-public-footer-link" href="{{ route('legal.privacy') }}">Política de privacidad</a></li>
                    <li><a class="sp-public-footer-link" href="{{ route('legal.faq') }}">Preguntas frecuentes</a></li>
                </ul>
            </div>
            <div class="sp-footer-contact">
                <h4 class="mb-4 font-semibold">Contacto</h4>
                <ul class="space-y-2 text-sm text-[#AAC8C4]">
                    <li><a class="sp-public-footer-link" href="mailto:comunicaciones@propaps.com">comunicaciones@propaps.com</a></li>
                    <li><a class="sp-public-footer-link" href="https://wa.me/573122851357" target="_blank" rel="noopener noreferrer">WhatsApp: +57 312 285 1357</a></li>
                </ul>
            </div>
        </div>
        
        <aside class="sp-academic-note" id="nota-inteligencia-hibrida" aria-label="Referencia académica de inteligencia híbrida" tabindex="-1">
            <p><span class="sp-note-number">1.</span> La inteligencia híbrida combina capacidades humanas y de inteligencia artificial. Referencia académica: Dellermann, D., Ebel, P., Söllner, M. y Leimeister, J. (2019). <a href="https://doi.org/10.1007/s12599-019-00595-2" target="_blank" rel="noopener noreferrer"><cite>Hybrid Intelligence</cite></a>. <em>Business &amp; Information Systems Engineering, 61</em>, 637–643.</p>
        </aside>

        <div class="sp-footer-copyright text-center text-[#AAC8C4]">
            <p>Todos los Derechos Reservados © 2026 Professional Papers S.A.S. – PROPAPS S.A.S.</p>
        </div>
    </div>
</footer>

<dialog class="sp-terms-modal" id="data-policy-modal" aria-labelledby="data-policy-modal-title">
    <div class="sp-terms-modal__panel">
        <div class="sp-terms-modal__header">
            <h2 id="data-policy-modal-title">Política de tratamiento de datos personales</h2>
            <button
                type="button"
                class="sp-terms-modal__close"
                data-public-modal-close
                aria-label="Cerrar politica de tratamiento de datos"
            >
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <div class="sp-terms-modal__content">
            @include('partials.legal.privacy')

        </div>
    </div>
</dialog>

<dialog class="sp-terms-modal" id="faq-modal" aria-labelledby="faq-modal-title">
    <div class="sp-terms-modal__panel">
        <div class="sp-terms-modal__header">
            <h2 id="faq-modal-title">Preguntas Frecuentes</h2>
            <button
                type="button"
                class="sp-terms-modal__close"
                data-public-modal-close
                aria-label="Cerrar preguntas frecuentes"
            >
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <div class="sp-terms-modal__content sp-faq-modal__content">
            @include('partials.legal.faq')

        </div>
    </div>
</dialog>

<dialog class="sp-terms-modal" id="terms-conditions-modal" aria-labelledby="terms-conditions-modal-title">
    <div class="sp-terms-modal__panel">
        <div class="sp-terms-modal__header">
            <h2 id="terms-conditions-modal-title">Términos y condiciones</h2>
            <button
                type="button"
                class="sp-terms-modal__close"
                data-public-modal-close
                aria-label="Cerrar terminos y condiciones"
            >
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <div class="sp-terms-modal__content">
            @include('partials.legal.terms')

        </div>
    </div>
</dialog>
