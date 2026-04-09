<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="sp-auth-page">
        <a href="{{ route('home', ['sp_navbar_anim' => 1]) }}" class="sp-auth-back-link" wire:navigate>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M14.5 5.5L8 12l6.5 6.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            <span>Volver al inicio</span>
        </a>

        <main class="sp-auth-main">
            {{ $slot }}
        </main>

        <script>
            document.querySelectorAll('[data-auth-shell]').forEach(function (shell) {
                const initial = shell.getAttribute('data-auth-initial');
                shell.classList.toggle('is-sign-up', initial === 'signup');
            });

            document.addEventListener('click', function (event) {
                const switchButton = event.target.closest('[data-auth-switch]');

                if (switchButton) {
                    const shell = switchButton.closest('[data-auth-shell]');
                    const target = switchButton.getAttribute('data-auth-switch');

                    if (shell && (target === 'signup' || target === 'signin')) {
                        shell.classList.toggle('is-sign-up', target === 'signup');
                        shell.setAttribute('data-auth-initial', target);
                    }

                    return;
                }

                const toggle = event.target.closest('[data-password-toggle]');

                if (! toggle) {
                    return;
                }

                const target = toggle.getAttribute('data-password-toggle');
                const input = target ? document.getElementById(target) : null;

                if (! input) {
                    return;
                }

                const showIcon = toggle.querySelector('[data-icon="show"]');
                const hideIcon = toggle.querySelector('[data-icon="hide"]');
                const willShow = input.type === 'password';

                input.type = willShow ? 'text' : 'password';
                showIcon?.classList.toggle('hidden', willShow);
                hideIcon?.classList.toggle('hidden', ! willShow);
                toggle.setAttribute('aria-label', willShow ? 'Ocultar contrasena' : 'Mostrar contrasena');
            });
        </script>
        @fluxScripts
    </body>
</html>
