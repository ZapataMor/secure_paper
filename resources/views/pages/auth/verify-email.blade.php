<x-layouts::auth.secure-papers :title="'Verificar correo'">
    @include('pages::auth.partials.account-style')
    <section class="sp-account">
        <span class="brand">Professional Papers</span>
        @if(config('accounts.email_verification_required'))
            <h1>Confirma tu correo</h1>
            <p>Abre el enlace que te enviamos para confirmar tu dirección. Si no lo encuentras, revisa el correo no deseado o solicita uno nuevo.</p>
            @if(session('status') === 'verification-link-sent')<p class="notice" role="status">Enviamos un nuevo enlace al correo de tu cuenta.</p>@endif
            <form method="POST" action="{{ route('verification.send') }}">@csrf<button type="submit">Reenviar enlace de verificación</button></form>
        @else
            <h1>Tu cuenta está disponible</h1>
            <p>Puedes continuar al inicio para consultar los servicios y tu proyecto.</p>
            <a class="back" href="{{ route('dashboard') }}">Continuar al inicio</a>
        @endif
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="secondary" type="submit">Cerrar sesión</button></form>
    </section>
</x-layouts::auth.secure-papers>
