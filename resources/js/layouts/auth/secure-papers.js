const syncAuthShellState = () => {
    document.querySelectorAll('[data-auth-shell]').forEach((shell) => {
        const initial = shell.getAttribute('data-auth-initial');
        shell.classList.toggle('is-sign-up', initial === 'signup');
    });
};

const bindAuthShellInteractions = () => {
    if (document.documentElement.dataset.authShellBound === 'true') {
        return;
    }

    document.documentElement.dataset.authShellBound = 'true';

    document.addEventListener('click', (event) => {
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

        if (!toggle) {
            return;
        }

        const target = toggle.getAttribute('data-password-toggle');
        const input = target ? document.getElementById(target) : null;

        if (!input) {
            return;
        }

        const showIcon = toggle.querySelector('[data-icon="show"]');
        const hideIcon = toggle.querySelector('[data-icon="hide"]');
        const willShow = input.type === 'password';

        input.type = willShow ? 'text' : 'password';
        showIcon?.classList.toggle('hidden', willShow);
        hideIcon?.classList.toggle('hidden', !willShow);
        toggle.setAttribute('aria-label', willShow ? 'Ocultar contrasena' : 'Mostrar contrasena');
    });
};

syncAuthShellState();
bindAuthShellInteractions();
