<x-layouts::auth.secure-papers :title="'Restablecer contraseña'">
    @include('pages::auth.partials.account-style')
    <section class="sp-account">
        <span class="brand">Professional Papers</span>
        <h1>Crea una nueva contraseña</h1>
        <p>Confirma tu correo y escribe la misma contraseña en ambos campos.</p>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <label for="reset-email">Correo electrónico</label>
            <input id="reset-email" name="email" type="email" value="{{ old('email', request('email')) }}" required autocomplete="email">
            @error('email')<p class="error" role="alert">{{ $message }}</p>@enderror
            <label for="reset-password">Nueva contraseña</label>
            <input id="reset-password" name="password" type="password" required autocomplete="new-password">
            @error('password')<p class="error" role="alert">{{ $message }}</p>@enderror
            <label for="reset-confirmation">Confirmar contraseña</label>
            <input id="reset-confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
            <button type="submit">Restablecer contraseña</button>
        </form>
        <a class="back" href="{{ route('login') }}">Volver al inicio de sesión</a>
    </section>
</x-layouts::auth.secure-papers>
