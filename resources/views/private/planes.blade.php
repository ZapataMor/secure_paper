<x-layouts::app :title="__('Pagos')">
    <div class="sp-payments sp-client-plans">
        <h1>Contrata tu servicio</h1>
        @if(config('wompi.environment') === 'sandbox')
            <p>Explora los servicios y prueba el recorrido de pago.</p>
        @else
            <p>Elige tu servicio. La carga de documentos se habilita cuando confirmamos tu pago.</p>
        @endif
        @if(session('status')) <div class="sp-payment-notice">{{ session('status') }}</div> @endif
        @if(session('membership_error')) <div class="sp-payment-notice">{{ session('membership_error') }}</div> @endif
        @if($errors->any()) <div role="alert">{{ $errors->first() }}</div> @endif
        @if(!$installed || !$gateway->ready())
            <div class="sp-payment-notice">Estamos preparando los pagos en línea. Puedes solicitar una cotización por <a href="https://wa.me/573122851357">WhatsApp</a>.</div>
        @elseif(config('wompi.environment') === 'sandbox')
            <div class="sp-payment-notice"><strong>Modo de pruebas.</strong> Los pagos simulados no representan cobros reales ni activan servicios reales o la carga de documentos en esta web.</div>
        @endif
        @if($trm)<p class="sp-payment-small">TRM vigente: {{ number_format($trm['units']/10000, 4, ',', '.') }} COP/USD. Fuente: Superfinanciera, datos.gov.co. Al iniciar el pago se confirma el importe.</p>
        @else<div class="sp-payment-notice">No pudimos consultar la TRM vigente. Los pagos permanecerán desactivados hasta verificarla.</div>@endif
        <div class="sp-payment-grid">
        @foreach($catalog as $key => $package)
            <section class="sp-payment-card" id="{{ $key }}">
                <h2>{{ $package['name'] }}</h2>
                @if($key === 'modo-indexacion')
                    <p class="sp-payment-small">Un año de acompañamiento editorial y carga de documentos desde la confirmación del pago.</p>
                @else
                    @php($agreement = app(\App\Services\ServiceAccessPolicy::class)->agreement($clientId, $key))
                    @if($agreement)<p class="sp-payment-small">Trabajo y ajustes acordados: {{ $agreement->start_date->format('d/m/Y') }} a {{ $agreement->end_date->format('d/m/Y') }}. La carga se habilita tras confirmar el pago.</p>
                    @else
                        <p class="sp-payment-small">Antes de pagar, necesitamos acordar contigo las fechas de trabajo y ajustes. El pago estará disponible cuando el equipo registre ese período.</p>
                        <a href="https://wa.me/573122851357">Acordar fechas por WhatsApp</a>
                    @endif
                @endif
                @foreach($package['options'] as $option => $variant)
                    @php($amount = $trm ? $gateway->standardAmount($key, $option, $clientId) : null)
                    <form method="POST" action="{{ route('private.payments.store') }}">
                        @csrf
                        <input type="hidden" name="package" value="{{ $key }}">
                        <input type="hidden" name="option" value="{{ $option }}">
                        <h3>{{ $variant['name'] }}</h3>
                        <p>{{ $variant['usd'] }} USD + IVA cuando corresponda</p>
                        @if($amount)
                            <strong>Total: ${{ number_format($amount / 100, 2, ',', '.') }} COP</strong>
                            <p class="sp-payment-small">TRM: {{ number_format($trm['units'] / 10000, 4, ',', '.') }} COP/USD. IVA: {{ $billingTax / 100 }} %.</p>
                        @else
                            <p class="sp-payment-small">El importe final en COP estará disponible antes de pagar.</p>
                        @endif
                        <button @disabled(!$installed || !$gateway->ready() || !$amount)>Continuar con Wompi</button>
                    </form>
                @endforeach
            </section>
        @endforeach
        </div>
        <section class="sp-payment-card"><h2>Pagos de servicios adicionales</h2>
            <p>Tarifas fijas. La TRM vigente se consulta al iniciar el pago; verás el total final antes de continuar a Wompi.</p>
            @if(!$canPayAdditional)<div class="sp-payment-notice">Puedes comprar estos adicionales después de pagar tu primer servicio.</div>@endif
            @if($billingTax === null)<p>Antes de pagar, verificaremos tus datos fiscales para aplicar el IVA que corresponda. <a href="https://wa.me/573122851357">Contactar a PROPAPS</a></p>@endif
            <div class="sp-payment-grid" style="grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr))">
            <form method="POST" action="{{ route('private.payments.store') }}">@csrf
                <h3>Pares evaluadores</h3><strong>160 USD + IVA cuando corresponda</strong>
                <p>El IVA se suma a la tarifa base. Selecciona el servicio al que se aplica la evaluación.</p>
                <input type="hidden" name="additional_type" value="peer_review">
                <label>Servicio<select name="package" required>@foreach($catalog as $key=>$package)<option value="{{ $key }}">{{ $package['name'] }}</option>@endforeach</select></label>
                <button @disabled(!$installed || !$gateway->ready() || !$canPayAdditional || $billingTax===null || !$trm)>Calcular y continuar</button>
            </form>
            <form method="POST" action="{{ route('private.payments.store') }}">@csrf
                <h3>Palabras adicionales · Corrección Completa</h3><strong>50 USD por cada bloque de hasta 2.000 palabras + IVA cuando corresponda</strong>
                <p>De 1 a 2.000: 50 USD; de 2.001 a 4.000: 100 USD; de 4.001 a 6.000: 150 USD, antes de impuestos.</p>
                <input type="hidden" name="package" value="correccion-completa"><input type="hidden" name="additional_type" value="extra_words">
                <label>Cantidad de palabras adicionales<input name="extra_words" type="number" min="1" max="1000000" step="1" required value="{{ old('extra_words') }}"></label>
                <button @disabled(!$installed || !$gateway->ready() || !$canPayAdditional || $billingTax===null || !$trm)>Calcular y continuar</button>
            </form></div>
            <p class="sp-payment-small">Los adicionales no amplían automáticamente la duración del acceso a documentos.</p>
        </section>
        @if($canPayAdditional && $quotes->isNotEmpty())<section class="sp-payment-card"><h2>Cobros anteriores pendientes</h2>
            @foreach($quotes as $quote)<form method="POST" action="{{ route('private.payments.store') }}">@csrf
                <input type="hidden" name="quote_id" value="{{ $quote->id }}"><h3>{{ $quote->description }}</h3>
                <p>{{ number_format($quote->usd_total_cents / 100, 2, ',', '.') }} USD. El total en COP se actualiza con la TRM al iniciar el pago.</p>
                <button @disabled(!$gateway->ready() || !$trm || $quote->usd_total_cents===null)>Revisar y pagar</button>
            </form>@endforeach
        </section>@endif
        @if($orders->isNotEmpty())<section class="sp-payment-card"><h2>Estado de tus pagos</h2>
            <p class="sp-payment-small">Últimos intentos de pago. Abre uno para consultar su detalle. Los marcados como «Prueba» no corresponden a cobros reales.</p>
            @foreach($orders as $order)
                @php($statusLabel = match($order->status) {
                    'APPROVED' => 'Aprobado',
                    'PENDING' => 'Pendiente de confirmación',
                    'DECLINED' => 'Rechazado',
                    'ERROR' => 'No completado por error',
                    'VOIDED' => 'Anulado',
                    default => 'Estado por verificar',
                })
                <p><a href="{{ route('private.payments.show', $order) }}">{{ $order->description }} · {{ $statusLabel }} · ${{ number_format($order->amount_in_cents / 100, 2, ',', '.') }} COP</a>
                    <span class="sp-payment-small sp-order-meta">{{ $order->created_at?->timezone('America/Bogota')->format('d/m/Y H:i') }} (hora de Colombia)@if($order->environment === 'sandbox') · <strong>Prueba</strong>@endif</span>
                </p>
            @endforeach
        </section>@endif
    </div>
    @include('partials.payments-style')
    <style>
        .sp-client-plans,.sp-client-plans .sp-payment-grid>*,.sp-client-plans form{min-width:0;max-width:100%;box-sizing:border-box}
        .sp-client-plans p,.sp-client-plans h1,.sp-client-plans h2,.sp-client-plans h3,.sp-client-plans a,.sp-client-plans strong{overflow-wrap:anywhere}
        .sp-client-plans input,.sp-client-plans select{min-width:0;max-width:100%;box-sizing:border-box}
        .sp-client-plans button{max-width:100%;white-space:normal}
        .sp-client-plans .sp-order-meta{display:block;margin-top:4px}
        .sp-client-plans a:focus-visible,.sp-client-plans button:focus-visible{outline:2px solid #2cc295;outline-offset:4px}
    </style>
</x-layouts::app>
