@if($quote->usd_total_cents !== null)
    @if($quote->additional_type === 'extra_words')
        <p>{{ number_format($quote->extra_words, 0, ',', '.') }} palabras adicionales · {{ $quote->units }} bloque(s) de hasta 2.000 palabras, a 50 USD por bloque.</p>
    @elseif($quote->additional_type === 'peer_review')
        <p>Pares evaluadores · Tarifa de referencia: 160 USD.</p>
    @endif
    <p>Subtotal: {{ number_format($quote->usd_subtotal_cents / 100, 2, ',', '.') }} USD<br>
       IVA ({{ $quote->tax_basis_points / 100 }} %): {{ number_format($quote->usd_tax_cents / 100, 2, ',', '.') }} USD<br>
       <strong>Total del servicio: {{ number_format($quote->usd_total_cents / 100, 2, ',', '.') }} USD</strong></p>
    @if($quote->tax_treatment === 'export_exempt')<p class="sp-payment-small">Servicio cotizado como exportación exenta de IVA, verificado por el administrador.</p>@endif
    <p class="sp-payment-small">Conversión acordada: {{ number_format($quote->cop_rate_units / 10000, 4, ',', '.') }} COP por USD. Wompi procesa el importe en COP; la cotización conserva esta tasa hasta su vencimiento.</p>
@endif
<strong>Importe a pagar con Wompi: ${{ number_format($quote->amount_in_cents / 100, 2, ',', '.') }} COP</strong>
