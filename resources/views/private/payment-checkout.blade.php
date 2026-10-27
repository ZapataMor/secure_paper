<x-layouts::app :title="__('Pago del servicio')">
@php($verification = $order->additional_type === 'integration_verification')
@php($paymentStatusUnavailable = $paymentStatusUnavailable ?? false)
<div class="sp-payments"><section class="sp-payment-card">
<h1>{{ $order->description }}</h1>
<p>Total: <strong>${{ number_format($order->amount_in_cents / 100, 2, ',', '.') }} COP</strong></p>
@include('partials.payment-order-price')
<p>Referencia: {{ $order->reference }}</p>
@if($verification)
    <div class="sp-payment-notice"><strong>Verificación con dinero real.</strong> Total final: $10.000 COP. Esta operación comprueba la conexión de pagos; no contrata un servicio ni habilita la carga de documentos.</div>
    <p class="sp-payment-small">Este intento vence el {{ $order->expires_at->copy()->timezone('America/Bogota')->format('d/m/Y H:i') }} (hora de Colombia). No hay cobro hasta completar el pago en Wompi.</p>
@endif
@if(!$order->is_additional)
    @if($order->package === 'modo-indexacion')<p>Incluye un año de acompañamiento editorial y carga de documentos desde la confirmación del pago.</p>
    @elseif($order->user_subscription_id && ($order->environment === 'production' || (config('wompi.sandbox_access') && !app()->environment('production'))))
        @php($access = \App\Models\UserSubscription::find($order->user_subscription_id))
        @if($access)<p>Período de trabajo y ajustes acordado: {{ $access->start_date->format('d/m/Y') }} a {{ $access->end_date->format('d/m/Y') }}.</p>@endif
    @endif
@endif
@if($order->environment === 'sandbox')<div class="sp-payment-notice">Modo de pruebas · Sin cobros reales.</div>@endif
@if($paymentStatusUnavailable)
    <div class="sp-payment-notice" role="status">
        <h2>No pudimos actualizar el estado del pago</h2>
        <p>No fue posible consultar el resultado en Wompi. Esto no confirma si se realizó el cobro. Conservamos el último estado confirmado de esta orden.</p>
        <p>Si ya autorizaste el pago, no lo repitas mientras verificamos el resultado.</p>
        <a class="sp-payment-action" href="{{ $statusRetryUrl }}">Consultar estado de nuevo</a>
    </div>
@endif
@if($order->status === 'APPROVED')
    <h2>Pago aprobado</h2>
    @if($order->environment === 'sandbox' && !$gateway->canApplyServiceChanges($order))
        <p>Pago de prueba confirmado. No modifica cotizaciones ni accesos reales.</p>
    @elseif($verification)<p>El pago real de verificación quedó confirmado. No activa servicios ni modifica el acceso a documentos.</p>
    @elseif($order->is_additional)<p>Tu servicio adicional quedó pagado. Este pago no modifica la duración del acceso.</p>
    @elseif($order->user_subscription_id && ($order->environment === 'production' || (config('wompi.sandbox_access') && !app()->environment('production'))))
        @if(auth()->user()->hasActiveMembership())<p>Tu acceso ya está habilitado durante el período vigente.</p><a class="sp-payment-action" href="{{ route('private.upload-document') }}">Cargar documentos</a>
        @else<p>El pago está confirmado. La carga de documentos estará disponible durante las fechas acordadas para tu servicio.</p>@endif
    @else<p>Pago de prueba confirmado. No activa acceso en producción.</p>@endif
@elseif($order->status === 'PENDING')
    @if(!$paymentStatusUnavailable)
    @if($verification)<p>Esperamos la confirmación de Wompi. Si ya pagaste, actualiza esta página para consultar el resultado de esta misma operación.</p>
    @else<p>Tu acceso permanece bloqueado hasta que Wompi confirme el pago. Si ya pagaste, actualiza esta página para consultar su estado.</p>@endif
    @if($gateway->ready() && $order->environment === config('wompi.environment') && !$order->transaction_id && $order->expires_at->isFuture())
    <form action="https://checkout.wompi.co/p/" method="GET">
        <input type="hidden" name="public-key" value="{{ config('wompi.public_key') }}">
        <input type="hidden" name="currency" value="COP">
        <input type="hidden" name="amount-in-cents" value="{{ $order->amount_in_cents }}">
        <input type="hidden" name="expiration-time" value="{{ $order->expires_at->copy()->utc()->toIso8601String() }}">
        <input type="hidden" name="reference" value="{{ $order->reference }}">
        <input type="hidden" name="signature:integrity" value="{{ $gateway->integrity($order) }}">
        <input type="hidden" name="redirect-url" value="{{ route('private.payments.show', $order) }}">
        <button>Ir al pago seguro de Wompi</button>
    </form>
    @elseif(!$order->transaction_id && $order->expires_at->isPast())
        <p>Este intento de pago venció. Vuelve a Pagos para consultar las opciones disponibles.</p>
    @endif
    @endif
@else
    <h2>{{ ['DECLINED'=>'Pago rechazado','VOIDED'=>'Pago anulado','ERROR'=>'No se pudo completar el pago'][$order->status] ?? 'Pago pendiente de revisión' }}</h2>
    @if(!$paymentStatusUnavailable)<p>Este pago no habilita la carga de documentos. Puedes volver a Pagos para iniciar un nuevo intento.</p>@endif
@endif
<p><a href="{{ route('private.planes') }}">Volver a Pagos</a></p>
</section></div>@include('partials.payments-style')
</x-layouts::app>

