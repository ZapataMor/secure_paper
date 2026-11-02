<?php

use App\Models\User;
use App\Models\WompiOrder;
use App\Services\AdditionalQuotePricing;
use App\Services\TrmService;
use App\Services\WompiGateway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

// Llaves falsas con el prefijo que exige WompiGateway::ready(); nunca llegan a Wompi (Http::fake).
beforeEach(function () {
    config([
        'wompi.environment' => 'production',
        'wompi.public_key' => 'pub_prod_fake',
        'wompi.private_key' => 'prv_prod_fake',
        'wompi.integrity_secret' => 'prod_integrity_fake',
        'wompi.events_secret' => 'prod_events_fake',
    ]);
});

test('el pago aprobado al volver de Wompi activa el servicio', function () {
    $client = wompiClient();
    $order = wompiOrder($client);
    fakeWompiTransaction($order, 'APPROVED');

    $this->actingAs($client)
        ->get(route('private.payments.show', ['order' => $order, 'id' => '556466-1791571531-22691']))
        ->assertOk();

    $order->refresh();
    expect($order->status)->toBe('APPROVED')
        ->and($order->transaction_id)->toBe('556466-1791571531-22691')
        ->and($order->user_subscription_id)->not->toBeNull();
    expect(DB::table('user_subscriptions')->where('user_id', $client->id)->value('payment_status'))->toBe('approved');
});

test('un error 500 de Wompi no cambia el pedido y muestra el estado no disponible', function () {
    $client = wompiClient();
    $order = wompiOrder($client);
    Http::fake(['production.wompi.co/v1/transactions/*' => Http::response(['error' => 'internal'], 500)]);

    $this->actingAs($client)
        ->get(route('private.payments.show', ['order' => $order, 'id' => '556466-1791571531-22691']))
        ->assertOk();

    $order->refresh();
    expect($order->status)->toBe('PENDING')
        ->and($order->transaction_id)->toBeNull();
});

test('el retorno rechaza una transacción con otra referencia', function () {
    $client = wompiClient();
    $order = wompiOrder($client);
    fakeWompiTransaction($order, 'APPROVED', ['reference' => 'PROPAPS-otra']);

    $this->actingAs($client)
        ->get(route('private.payments.show', ['order' => $order, 'id' => '556466-1791571531-22691']))
        ->assertStatus(422);

    expect($order->refresh()->status)->toBe('PENDING');
});

test('el webhook firmado de Wompi aprueba el pedido', function () {
    $client = wompiClient();
    $order = wompiOrder($client);
    fakeWompiTransaction($order, 'APPROVED');

    $this->postJson(route('wompi.webhook', 'production'), wompiEvent($order, 'APPROVED'))
        ->assertOk();

    expect($order->refresh()->status)->toBe('APPROVED');
});

test('el webhook con firma inválida se rechaza', function () {
    $client = wompiClient();
    $order = wompiOrder($client);
    $event = wompiEvent($order, 'APPROVED');
    $event['signature']['checksum'] = str_repeat('0', 64);

    $this->postJson(route('wompi.webhook', 'production'), $event)->assertStatus(401);

    expect($order->refresh()->status)->toBe('PENDING');
});

test('la firma de integridad sigue la fórmula de Wompi', function () {
    $order = wompiOrder(wompiClient());
    $expected = hash('sha256', $order->reference.$order->amount_in_cents.'COP'.$order->expires_at->copy()->utc()->toIso8601String().'prod_integrity_fake');

    expect(app(WompiGateway::class)->integrity($order))->toBe($expected);
});

test('la conversión a COP cobra pesos enteros, sin centavos', function () {
    $trm = app(TrmService::class);
    // 606,90 USD con TRM 3.182,99 = 1.931.756,63 COP: Wompi lo rechazó por los centavos.
    expect($trm->convert(60690, ['units' => 31829900]))->toBe(193175700)
        ->and($trm->convert(60690, ['units' => 31829100]))->toBe(193170800)
        ->and($trm->convert(100, ['units' => 40005000]))->toBe(400100);
});

test('el pedido de Modo Indexación se crea con un monto sin centavos', function () {
    $client = wompiClient();
    $client->forceFill(['payment_billing_country' => 'CO', 'payment_tax_basis_points' => 1900, 'payment_billing_verified_at' => now()])->save();
    Cache::put('propaps-trm-'.now('America/Bogota')->toDateString(), [
        'units' => 31829900, 'from' => now('America/Bogota')->toDateString(),
        'until' => now('America/Bogota')->toDateString(), 'source' => 'prueba',
    ], 300);

    $order = app(WompiGateway::class)->createOrder($client->id, ['package' => 'modo-indexacion', 'option' => 'pregrado']);

    expect($order->amount_in_cents)->toBe(193175700)
        ->and($order->amount_in_cents % 100)->toBe(0);
});

test('las cotizaciones adicionales también se cobran en pesos enteros', function () {
    $quote = app(AdditionalQuotePricing::class)->calculate([
        'additional_type' => 'peer_review', 'package' => 'modo-indexacion', 'billing_country' => 'CO',
        'tax_treatment' => 'standard', 'cop_per_usd' => '3182.99',
    ]);

    expect($quote['amount_in_cents'] % 100)->toBe(0);
});

function wompiClient(): User
{
    $id = DB::table('users')->insertGetId([
        'role' => 'client', 'name' => 'Cliente', 'last_name' => 'Wompi', 'email' => 'cliente-wompi@securepapers.test',
        'password' => Hash::make('password'), 'status' => 'active', 'email_verified_at' => now(),
        'created_at' => now(), 'updated_at' => now(),
    ]);

    return User::findOrFail($id);
}

function wompiOrder(User $client): WompiOrder
{
    return WompiOrder::create([
        'user_id' => $client->id, 'reference' => 'PROPAPS-test-'.uniqid(), 'environment' => 'production',
        'package' => 'correccion-completa', 'description' => 'Corrección Completa · Hasta 40.000 palabras',
        'amount_in_cents' => 250000000, 'currency' => 'COP', 'access_days' => 30, 'status' => 'PENDING',
        'is_additional' => false, 'expires_at' => now()->addHour(),
    ]);
}

function wompiTransaction(WompiOrder $order, string $status, array $overrides = []): array
{
    return array_merge([
        'id' => '556466-1791571531-22691', 'reference' => $order->reference, 'status' => $status,
        'amount_in_cents' => $order->amount_in_cents, 'currency' => 'COP',
    ], $overrides);
}

function fakeWompiTransaction(WompiOrder $order, string $status, array $overrides = []): void
{
    Http::fake(['production.wompi.co/v1/transactions/*' => Http::response(['data' => wompiTransaction($order, $status, $overrides)])]);
}

function wompiEvent(WompiOrder $order, string $status): array
{
    $transaction = wompiTransaction($order, $status);
    $timestamp = now()->timestamp;
    $properties = ['transaction.id', 'transaction.status', 'transaction.amount_in_cents'];

    return [
        'event' => 'transaction.updated', 'environment' => 'prod', 'timestamp' => $timestamp,
        'data' => ['transaction' => $transaction],
        'signature' => [
            'properties' => $properties,
            'checksum' => hash('sha256', $transaction['id'].$transaction['status'].$transaction['amount_in_cents'].$timestamp.'prod_events_fake'),
        ],
    ];
}
