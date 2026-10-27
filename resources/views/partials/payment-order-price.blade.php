@if($order->usd_total_cents !== null)
<p>Subtotal: {{ number_format($order->usd_subtotal_cents / 100, 2, ',', '.') }} USD<br>
IVA ({{ $order->tax_basis_points / 100 }} %): {{ number_format($order->usd_tax_cents / 100, 2, ',', '.') }} USD<br>
<strong>Total del servicio: {{ number_format($order->usd_total_cents / 100, 2, ',', '.') }} USD</strong></p>
@if($order->additional_type === 'extra_words')<p>{{ $order->extra_words }} palabras adicionales · {{ $order->additional_units }} bloque(s) de hasta 2.000 palabras.</p>@endif
<p>TRM aplicada: {{ number_format($order->cop_rate_units / 10000, 4, ',', '.') }} COP/USD.<br>
Vigencia: {{ $order->trm_valid_from }} a {{ $order->trm_valid_until }}. Fuente: {{ $order->trm_source }}.</p>
<p class="sp-payment-small">El importe de este intento permanece fijo. Vence el {{ $order->expires_at->copy()->timezone('America/Bogota')->format('d/m/Y H:i') }} (hora de Colombia). No hay cobro hasta completar el pago en Wompi.</p>
@endif
