<x-layouts::app :title="__('Cotizaciones')">
<div class="sp-payments"><h1>Administración de pagos</h1>
<p>Los adicionales se compran directamente con tarifa en USD y TRM vigente al iniciar el pago. Verifica aquí el tratamiento fiscal de cada cliente.</p>
@if(session('status'))<div class="sp-payment-notice">{{ session('status') }}</div>@endif
@if($errors->any())<div role="alert">{{ $errors->first() }}</div>@endif
@if(!$installed)<div class="sp-payment-notice">El módulo necesita completar su instalación antes de crear cotizaciones.</div>@endif
<form method="POST" action="{{ route('admin.payment-billing.store') }}" class="sp-payment-card">@csrf
<h2>Verificar datos fiscales del cliente</h2>
<p>Las tarifas adicionales son fijas y el cliente las paga directamente. Esta verificación determina el IVA; no crea una cotización ni habilita documentos.</p>
<label>Cliente<select name="user_id" required>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }} · {{ $client->email }}</option>@endforeach</select></label>
<label>País del cliente (CO, US, ES...)<input name="country" maxlength="2" pattern="[A-Z]{2}" required></label>
<label>IVA<select name="tax" required><option value="">Selecciona después de revisar el caso</option><option value="1900">19 %</option><option value="0">Exportación de servicios exenta · 0 %</option></select></label>
<label><input style="display:inline;width:auto" type="checkbox" name="export_verified" value="1"> He verificado las condiciones y conservado los soportes de exportación: servicio prestado desde Colombia, uso exclusivo en el exterior y beneficiario sin negocios o actividades en Colombia.</label>
<button>Guardar verificación fiscal</button></form>
<section class="sp-payment-card"><h2>Período de trabajo y ajustes</h2>
<p>Modo Indexación habilita la carga durante un año desde el pago confirmado. Para Corrección Completa y Grandes Volúmenes, registra las fechas acordadas por cada servicio del cliente.</p>
<form method="POST" action="{{ route('admin.service-access.store') }}">@csrf
<h3>Registrar un nuevo acuerdo</h3>
<label>Cliente<select name="user_id" required>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }} · {{ $client->email }}</option>@endforeach</select></label>
<label>Servicio<select name="package" required><option value="correccion-completa">Corrección Completa</option><option value="grandes-volumenes">Grandes Volúmenes</option></select></label>
<div class="sp-payment-grid"><label>Inicio acordado<input type="date" name="start_date" required value="{{ old('start_date') }}"></label><label>Fin de trabajo y ajustes<input type="date" name="end_date" required min="{{ now()->toDateString() }}" value="{{ old('end_date') }}"></label></div>
<p class="sp-payment-small">Este registro no aprueba pagos. La carga se habilita cuando el pago esté confirmado y el período esté vigente.</p>
<button>Guardar período acordado</button>
</form></section>
<section class="sp-payment-card"><h2>Vigencias registradas por servicio</h2>
@forelse($serviceAccess as $access)
<div style="border-top:1px solid #2cc29544;padding:16px 0">
<h3>{{ $access->user?->name }} · {{ $access->paymentPlan?->name }}</h3>
<p>Servicio #{{ $access->id }} · {{ $access->payment_status === 'pending' ? 'Pago pendiente' : 'Estado del pago: '.$access->payment_status }}</p>
@if(!in_array($access->status, ['cancelled', 'canceled', 'voided'], true))
<form method="POST" action="{{ route('admin.service-access.store') }}">@csrf
<input type="hidden" name="subscription_id" value="{{ $access->id }}"><input type="hidden" name="user_id" value="{{ $access->user_id }}">
<input type="hidden" name="package" value="{{ $access->paymentPlan?->name === app(\App\Services\ServiceAccessPolicy::class)->planName('correccion-completa') ? 'correccion-completa' : 'grandes-volumenes' }}">
<div class="sp-payment-grid"><label>Inicio acordado<input type="date" name="start_date" required value="{{ $access->start_date->toDateString() }}"></label><label>Fin de trabajo y ajustes<input type="date" name="end_date" required min="{{ now()->toDateString() }}" value="{{ $access->end_date->toDateString() }}"></label></div>
<button>Actualizar fechas de este servicio</button></form>
@endif
</div>
@empty<p>No hay fechas registradas para estos servicios.</p>@endforelse
</section>
<section class="sp-payment-card"><h2>Estado fiscal</h2>@foreach($clients as $client)<p>{{ $client->name }} · {{ $client->payment_billing_verified_at ? 'Verificado: '.$client->payment_billing_country.' · IVA '.($client->payment_tax_basis_points / 100).' %' : 'Pendiente' }}</p>@endforeach</section>
<section class="sp-payment-card"><h2>Cotizaciones recientes</h2>@foreach($quotes as $quote)<div style="border-top:1px solid #2cc29544;padding:16px 0"><h3>#{{ $quote->id }} · Cliente {{ $quote->user_id }} · {{ $quote->description }}</h3>@include('partials.additional-price', ['quote'=>$quote])<p>Estado: {{ $quote->status }}</p></div>@endforeach</section>
</div>@include('partials.payments-style')
</x-layouts::app>
