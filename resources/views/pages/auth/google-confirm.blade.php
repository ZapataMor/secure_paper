<x-layouts::auth.secure-papers :title="'Completar registro'">
    <style>
        .sp-google-confirm{width:min(460px,100%);margin:auto;padding:30px;border:1px solid #14594c;border-radius:24px;background:#062924;color:#e7f4ef;box-sizing:border-box}
        .sp-google-confirm h1{font-size:28px;line-height:1.2;margin:8px 0 16px}.sp-google-confirm p{line-height:1.6;color:#b4d0c7;overflow-wrap:anywhere}
        .sp-google-confirm .eyebrow{color:#27c99c;font-size:12px;letter-spacing:.1em;text-transform:uppercase}.sp-google-confirm label{display:flex;gap:12px;font-size:14px;line-height:1.6;margin:24px 0}
        .sp-google-confirm input{margin-top:6px;accent-color:#24c99b;flex-shrink:0}.sp-google-confirm a{color:#47dab1;text-decoration:underline}
        .sp-google-confirm button{width:100%;padding:14px;border:0;border-radius:12px;background:#23c79b;color:#002d23;font-weight:700;cursor:pointer}
        .sp-google-confirm .error{color:#ffb5ac;font-size:14px}.sp-google-confirm .cancel{display:block;text-align:center;margin-top:20px}
        @media(max-width:480px){.sp-google-confirm{padding:24px}.sp-google-confirm h1{font-size:25px}}
    </style>
    <section class="sp-google-confirm">
        <span class="eyebrow">Professional Papers</span>
        <h1>Completa tu registro</h1>
        <p>Google confirmó tu correo <strong>{{ $profile['email'] }}</strong>. Revisa las condiciones para crear tu cuenta y empezar.</p>
        <form method="POST" action="{{ route('google.register') }}">
            @csrf
            <label><input type="checkbox" name="terms" value="1" required><span>Acepto los <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener">términos y condiciones</a> y la <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">política de privacidad</a>.</span></label>
            @error('terms')<p class="error" role="alert">{{ $message }}</p>@enderror
            <button type="submit">Crear mi cuenta</button>
        </form>
        <a class="cancel" href="{{ route('login') }}">Volver al inicio de sesión</a>
    </section>
</x-layouts::auth.secure-papers>
