<x-layouts::app :title="__('Prueba de avisos')">
    <section class="sp-admin-page">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">Administración</p>
            <h1>Prueba de avisos</h1>
            <p>Comprueba que los correos de Propaps llegan a tu cuenta.</p>
        </header>
        <div class="max-w-2xl rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-6 text-[#F1F7F6]">
            <h2 class="text-xl font-semibold">Un correo a tu cuenta de Gerencia</h2>
            <p class="mt-3 text-[#AAC8C4]">Enviaremos un mensaje identificado como prueba a <strong class="text-[#F1F7F6]">{{ $recipientEmail }}</strong>. No se modificará ningún proyecto, documento ni pago.</p>
            @if(session('mail_test_queued'))
                <p role="status" class="mt-5 rounded-xl border border-[#2CC295]/50 bg-[#2CC295]/10 p-4">La prueba quedó en cola. El envío puede tardar alrededor de un minuto. Revisa la bandeja de entrada y spam de tu cuenta de Gerencia.</p>
            @endif
            @error('mail_test')
                <p role="alert" class="mt-5 rounded-xl border border-[#F9BC60]/50 p-4 text-[#FAD7A0]">{{ $message }}</p>
            @enderror
            @if(!$ready)
                <p class="mt-5 text-[#FAD7A0]">Los avisos todavía no están disponibles para esta prueba.</p>
            @endif
            <form method="POST" action="{{ route('admin.mail-test.store') }}" class="mt-6">
                @csrf
                <button type="submit" @disabled(!$ready || session('mail_test_queued')) class="rounded-xl bg-[#2CC295] px-5 py-3 font-semibold text-[#032221] disabled:cursor-not-allowed disabled:opacity-50">Enviar correo de prueba a mi cuenta</button>
            </form>
            <p class="mt-4 text-sm text-[#AAC8C4]">Puedes solicitar una prueba cada diez minutos.</p>
            <a class="mt-6 inline-block text-[#2CC295]" href="{{ route('dashboard') }}">Volver al panel →</a>
        </div>
    </section>
</x-layouts::app>
