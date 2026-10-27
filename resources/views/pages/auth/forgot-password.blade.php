<x-layouts::auth.secure-papers :title="'Crear o recuperar contraseña'">
    @include('pages::auth.partials.account-style')
    <section class="sp-account">
        <span class="brand">Professional Papers</span>
        <h1>Crear o recuperar contraseña</h1>
        <p>Escribe el correo de tu cuenta de Propaps para recibir un enlace y crear una contraseña propia de la aplicación.</p>
        <p>Si te registraste con Google, puedes seguir ingresando con Google. Esta opción permite establecer una contraseña de Propaps para usar también el formulario de correo y contraseña; no cambia tu contraseña de Google.</p>
        @if(session('status'))<p class="notice" role="status">{{ session('status') === __('passwords.sent') ? 'Te enviamos un enlace para recuperar tu contraseña. Revisa también la carpeta de correo no deseado.' : session('status') }}</p>@endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="recovery-email">Correo electrónico</label>
            <input id="recovery-email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="tu@correo.com">
            @error('email')<p class="error" role="alert">{{ $message }}</p>@enderror
            <button type="submit">Enviar enlace para mi contraseña</button>
        </form>
        <a class="back" href="{{ route('login') }}">Volver al inicio de sesión</a>
    </section>
</x-layouts::auth.secure-papers>
