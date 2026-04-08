@php
    $baseMode = $mode ?? 'login';
    $hasRegisterContext = $errors->has('name')
        || $errors->has('password_confirmation')
        || old('name')
        || old('password_confirmation');
    $isSignUp = $baseMode === 'register' || $hasRegisterContext;

    $loginEmailValue = $isSignUp ? '' : old('email');
    $registerNameValue = $isSignUp ? old('name') : '';
    $registerEmailValue = $isSignUp ? old('email') : '';

    $loginEmailError = $isSignUp ? null : $errors->first('email');
    $loginPasswordError = $isSignUp ? null : $errors->first('password');

    $registerNameError = $isSignUp ? $errors->first('name') : null;
    $registerEmailError = $isSignUp ? $errors->first('email') : null;
    $registerPasswordError = $isSignUp ? $errors->first('password') : null;
    $registerPasswordConfirmationError = $isSignUp ? $errors->first('password_confirmation') : null;
@endphp

<section class="sp-auth-shell {{ $isSignUp ? 'is-sign-up' : '' }}" data-auth-shell data-auth-initial="{{ $isSignUp ? 'signup' : 'signin' }}">
    <div class="sp-auth-form-stage">
        <div class="sp-auth-pane sp-auth-pane--signup">
            <div class="sp-auth-form-inner">
                <h1 class="sp-auth-title">
                    <svg class="sp-auth-title-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3l7 3v5c0 5-3.4 8.9-7 10-3.6-1.1-7-5-7-10V6l7-3z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                        <path d="M9.5 12.5l1.8 1.8 3.3-3.3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                    </svg>
                    Crear cuenta
                </h1>

                <form method="POST" action="{{ route('register.store') }}" class="sp-auth-form">
                    @csrf

                    <div class="sp-auth-input-block">
                        <label for="register-name" class="sp-auth-label">Nombre completo</label>
                        <div class="sp-auth-input-frame">
                            <svg class="sp-auth-input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.75" />
                                <path d="M4 19c1.8-3 4.4-4.5 8-4.5s6.2 1.5 8 4.5" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                            </svg>
                            <input
                                id="register-name"
                                name="name"
                                type="text"
                                value="{{ $registerNameValue }}"
                                required
                                @if ($isSignUp) autofocus @endif
                                autocomplete="name"
                                placeholder="Ingresa tu nombre"
                                class="sp-auth-input {{ $registerNameError ? 'sp-auth-input--error' : '' }}"
                            />
                        </div>
                        @if ($registerNameError)
                            <p class="sp-auth-error">{{ $registerNameError }}</p>
                        @endif
                    </div>

                    <div class="sp-auth-input-block">
                        <label for="register-email" class="sp-auth-label">Correo electronico</label>
                        <div class="sp-auth-input-frame">
                            <svg class="sp-auth-input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" stroke="currentColor" stroke-linejoin="round" stroke-width="1.75" />
                                <path d="M4 7l8 6 8-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                            </svg>
                            <input
                                id="register-email"
                                name="email"
                                type="email"
                                value="{{ $registerEmailValue }}"
                                required
                                autocomplete="email"
                                placeholder="correo@ejemplo.com"
                                class="sp-auth-input {{ $registerEmailError ? 'sp-auth-input--error' : '' }}"
                            />
                        </div>
                        @if ($registerEmailError)
                            <p class="sp-auth-error">{{ $registerEmailError }}</p>
                        @endif
                    </div>

                    <div class="sp-auth-input-block">
                        <label for="register-password" class="sp-auth-label">Contrasena</label>
                        <div class="sp-auth-input-frame">
                            <svg class="sp-auth-input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.75" />
                                <path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                            </svg>
                            <input
                                id="register-password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Crea una contrasena segura"
                                class="sp-auth-input sp-auth-input--with-toggle {{ $registerPasswordError ? 'sp-auth-input--error' : '' }}"
                            />
                            <button type="button" class="sp-auth-password-toggle" data-password-toggle="register-password" aria-label="Mostrar contrasena">
                                <span data-icon="show">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M2 12s3.7-6 10-6 10 6 10 6-3.7 6-10 6-10-6-10-6z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.75" />
                                    </svg>
                                </span>
                                <span data-icon="hide" class="hidden">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M3 3l18 18" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M10.6 10.6a2 2 0 102.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M9.9 4.4A12.8 12.8 0 0112 4c6.3 0 10 6 10 6a17.5 17.5 0 01-3.3 4.2" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M6.2 6.2C3.7 8 2 10 2 10s3.7 6 10 6c1.5 0 2.9-.3 4.1-.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        @if ($registerPasswordError)
                            <p class="sp-auth-error">{{ $registerPasswordError }}</p>
                        @endif
                    </div>

                    <div class="sp-auth-input-block">
                        <label for="register-password-confirmation" class="sp-auth-label">Confirmar contrasena</label>
                        <div class="sp-auth-input-frame">
                            <svg class="sp-auth-input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.75" />
                                <path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                            </svg>
                            <input
                                id="register-password-confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Confirma tu contrasena"
                                class="sp-auth-input sp-auth-input--with-toggle {{ $registerPasswordConfirmationError ? 'sp-auth-input--error' : '' }}"
                            />
                            <button type="button" class="sp-auth-password-toggle" data-password-toggle="register-password-confirmation" aria-label="Mostrar contrasena">
                                <span data-icon="show">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M2 12s3.7-6 10-6 10 6 10 6-3.7 6-10 6-10-6-10-6z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.75" />
                                    </svg>
                                </span>
                                <span data-icon="hide" class="hidden">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M3 3l18 18" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M10.6 10.6a2 2 0 102.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M9.9 4.4A12.8 12.8 0 0112 4c6.3 0 10 6 10 6a17.5 17.5 0 01-3.3 4.2" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M6.2 6.2C3.7 8 2 10 2 10s3.7 6 10 6c1.5 0 2.9-.3 4.1-.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        @if ($registerPasswordConfirmationError)
                            <p class="sp-auth-error">{{ $registerPasswordConfirmationError }}</p>
                        @endif
                    </div>

                    <label class="sp-auth-checkline">
                        <input type="checkbox" name="terms" @checked(old('terms')) />
                        <p>
                            Acepto los <a href="{{ route('home') }}" class="sp-auth-link" wire:navigate>terminos</a> y la
                            <a href="{{ route('home') }}" class="sp-auth-link" wire:navigate>politica de privacidad</a>.
                        </p>
                    </label>

                    <button type="submit" class="sp-auth-submit-btn" data-test="register-user-button">
                        Registrarme
                    </button>
                </form>

                <div class="sp-auth-mobile-switch">
                    <span>Ya tienes una cuenta?</span>
                    <button type="button" data-auth-switch="signin" class="sp-auth-link-btn">Iniciar sesion</button>
                </div>
            </div>
        </div>

        <div class="sp-auth-pane sp-auth-pane--signin">
            <div class="sp-auth-form-inner">
                <h1 class="sp-auth-title">
                    <svg class="sp-auth-title-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3l7 3v5c0 5-3.4 8.9-7 10-3.6-1.1-7-5-7-10V6l7-3z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                        <path d="M9.5 12.5l1.8 1.8 3.3-3.3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                    </svg>
                    Iniciar sesion
                </h1>

                @if (session('status') && ! $isSignUp)
                    <p class="sp-auth-status">{{ session('status') }}</p>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="sp-auth-form">
                    @csrf

                    <div class="sp-auth-input-block">
                        <label for="login-email" class="sp-auth-label">Correo electronico</label>
                        <div class="sp-auth-input-frame">
                            <svg class="sp-auth-input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" stroke="currentColor" stroke-linejoin="round" stroke-width="1.75" />
                                <path d="M4 7l8 6 8-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                            </svg>
                            <input
                                id="login-email"
                                name="email"
                                type="email"
                                value="{{ $loginEmailValue }}"
                                required
                                @if (! $isSignUp) autofocus @endif
                                autocomplete="email"
                                placeholder="correo@ejemplo.com"
                                class="sp-auth-input {{ $loginEmailError ? 'sp-auth-input--error' : '' }}"
                            />
                        </div>
                        @if ($loginEmailError)
                            <p class="sp-auth-error">{{ $loginEmailError }}</p>
                        @endif
                    </div>

                    <div class="sp-auth-input-block">
                        <label for="login-password" class="sp-auth-label">Contrasena</label>
                        <div class="sp-auth-input-frame">
                            <svg class="sp-auth-input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.75" />
                                <path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                            </svg>
                            <input
                                id="login-password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Ingresa tu contrasena"
                                class="sp-auth-input sp-auth-input--with-toggle {{ $loginPasswordError ? 'sp-auth-input--error' : '' }}"
                            />
                            <button type="button" class="sp-auth-password-toggle" data-password-toggle="login-password" aria-label="Mostrar contrasena">
                                <span data-icon="show">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M2 12s3.7-6 10-6 10 6 10 6-3.7 6-10 6-10-6-10-6z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.75" />
                                    </svg>
                                </span>
                                <span data-icon="hide" class="hidden">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M3 3l18 18" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M10.6 10.6a2 2 0 102.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M9.9 4.4A12.8 12.8 0 0112 4c6.3 0 10 6 10 6a17.5 17.5 0 01-3.3 4.2" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                        <path d="M6.2 6.2C3.7 8 2 10 2 10s3.7 6 10 6c1.5 0 2.9-.3 4.1-.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.75" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        @if ($loginPasswordError)
                            <p class="sp-auth-error">{{ $loginPasswordError }}</p>
                        @endif
                    </div>

                    <div class="sp-auth-row">
                        <label class="sp-auth-inline-check">
                            <input type="checkbox" name="remember" @checked(old('remember')) />
                            <span>Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="sp-auth-link" href="{{ route('password.request') }}" wire:navigate>
                                Olvide mi contrasena
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="sp-auth-submit-btn" data-test="login-button">
                        Ingresar
                    </button>
                </form>

                <div class="sp-auth-mobile-switch">
                    <span>No tienes cuenta?</span>
                    <button type="button" data-auth-switch="signup" class="sp-auth-link-btn">Crear cuenta</button>
                </div>
            </div>
        </div>
    </div>

    <div class="sp-auth-toggle-shell" aria-hidden="true">
        <div class="sp-auth-toggle-track">
            <div class="sp-auth-toggle-panel sp-auth-toggle-panel--left">
                <h2 class="sp-auth-toggle-title">Bienvenido de nuevo</h2>
                <p class="sp-auth-toggle-text">
                    Si ya tienes una cuenta, inicia sesion y continua gestionando tus documentos en segundos.
                </p>
                <button type="button" data-auth-switch="signin" class="sp-auth-ghost-btn">
                    Iniciar sesion
                </button>
            </div>

            <div class="sp-auth-toggle-panel sp-auth-toggle-panel--right">
                <h2 class="sp-auth-toggle-title">Nuevo en Secure Papers?</h2>
                <p class="sp-auth-toggle-text">
                    Crea tu cuenta y comienza a validar tus investigaciones con revisores especializados.
                </p>
                <button type="button" data-auth-switch="signup" class="sp-auth-ghost-btn">
                    Crear cuenta
                </button>
            </div>
        </div>
    </div>
</section>
