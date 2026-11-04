<?php

use App\Models\User;
use App\Models\WompiOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');
    Http::preventStrayRequests();
    Mail::fake();
    Notification::fake();
    Queue::fake();
    config(['wompi.sandbox_access' => false]);
    $day = now('America/Bogota')->toDateString();
    Cache::put('propaps-trm-'.$day, [
        'units' => 40000000, 'from' => $day, 'until' => $day, 'source' => 'Prueba ficticia',
    ], 300);
});

afterEach(function () {
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
    Queue::assertNothingPushed();
});

test('client without paid membership cannot access upload module', function () {
    $client = createUser('client', 'Ana', 'Sin Plan', 'ana-sin-plan@securepapers.test');

    $uploadResponse = $this
        ->actingAs($client)
        ->get(route('private.upload-document'));

    $uploadResponse
        ->assertRedirect(route('private.planes'))
        ->assertSessionHas('membership_error');

    $dashboardResponse = $this
        ->actingAs($client)
        ->get(route('dashboard'));

    $dashboardResponse
        ->assertOk()
        ->assertSee('Disponible con un servicio activo y pagado.')
        ->assertSee('disabled aria-describedby="sp-start-access-note"', false);

    $this->actingAs($client)->postJson(route('private.upload-document.store'), ['submission_id' => (string) Str::uuid(),
        'additional_information' => 'Intento ficticio sin servicio',
    ])->assertForbidden();
    expect($client->fresh()->additionalInformationEntries())->toHaveCount(0);
});

test('client with active paid membership can access upload module', function () {
    $client = createUser('client', 'Luis', 'Con Plan', 'luis-con-plan@securepapers.test');

    $planId = createPlan('Premium');
    createActivePaidSubscription($client->id, $planId);

    $response = $this
        ->actingAs($client)
        ->get(route('private.upload-document'));

    $response->assertOk();

    $dashboardResponse = $this
        ->actingAs($client)
        ->get(route('dashboard'));

    $dashboardResponse
        ->assertOk()
        ->assertSee('Cargar documentos →')
        ->assertDontSee('disabled aria-describedby="sp-start-access-note"', false);
});

test('selecting a service only grants access after a verified eligible approval', function (string $environment, string $status, bool $grantsAccess) {
    // Credenciales ficticias y respuestas interceptadas: nunca se contacta a Wompi.
    $prefix = $environment === 'production' ? 'prod' : 'test';
    config([
        'wompi.environment' => $environment,
        'wompi.public_key' => 'pub_'.$prefix.'_fake',
        'wompi.private_key' => 'prv_'.$prefix.'_fake',
        'wompi.integrity_secret' => $prefix.'_integrity_fake',
        'wompi.events_secret' => $prefix.'_events_fake',
    ]);
    $client = createUser('client', 'Camila', 'Pendiente', 'camila-plan-pendiente@securepapers.test');
    $client->forceFill([
        'payment_billing_country' => 'CO', 'payment_tax_basis_points' => 1900,
        'payment_billing_verified_at' => now(),
    ])->save();

    $this->actingAs($client)->get(route('private.planes', ['paquete' => 'modo-indexacion']))->assertOk();
    $this->assertDatabaseCount('wompi_orders', 0);
    $this->assertDatabaseCount('user_subscriptions', 0);

    $selection = ['package' => 'modo-indexacion', 'option' => 'pregrado'];
    $this->post(route('private.payments.store'), $selection)->assertRedirect();
    $order = WompiOrder::sole();
    expect($order->status)->toBe('PENDING')->and($order->user_id)->toBe($client->id);
    $this->post(route('private.payments.store'), $selection)
        ->assertRedirect(route('private.payments.show', $order));
    $this->assertDatabaseCount('wompi_orders', 1);

    // Un estado escrito en la URL no demuestra que se haya pagado.
    $this->get(route('private.payments.show', ['order' => $order, 'status' => 'APPROVED']))->assertOk();
    expect($order->fresh()->status)->toBe('PENDING');
    $this->assertDatabaseCount('user_subscriptions', 0);
    $this->get(route('private.upload-document'))->assertRedirect(route('private.planes'));
    $this->postJson(route('private.upload-document.store'), ['submission_id' => (string) Str::uuid(), 'additional_information' => 'Sin pago'])->assertForbidden();
    Http::assertNothingSent();

    $transactionId = 'prueba-local-16';
    $api = $environment === 'production' ? 'production' : 'sandbox';
    Http::fake(["$api.wompi.co/v1/transactions/$transactionId" => Http::response(['data' => [
        'id' => $transactionId, 'reference' => $order->reference, 'status' => $status,
        'amount_in_cents' => $order->amount_in_cents, 'currency' => 'COP',
    ]])]);
    $return = route('private.payments.show', ['order' => $order, 'id' => $transactionId]);
    $this->get($return)->assertOk();
    // Repetir el retorno no duplica la suscripción.
    $this->get($return)->assertOk();
    Http::assertSentCount(2);
    expect($order->fresh()->status)->toBe($status)
        ->and($client->fresh()->hasActiveMembership())->toBe($grantsAccess);
    $this->assertDatabaseCount('user_subscriptions', $grantsAccess ? 1 : 0);
    $this->assertDatabaseCount('documents', 0);
    expect($client->fresh()->additionalInformationEntries())->toHaveCount(0);
    if ($grantsAccess) {
        $this->get(route('private.upload-document'))->assertOk();
        $this->get(route('dashboard'))->assertOk()->assertSee('Cargar documentos →');
    } else {
        $this->get(route('private.upload-document'))->assertRedirect(route('private.planes'));
        $this->postJson(route('private.upload-document.store'), ['submission_id' => (string) Str::uuid(), 'additional_information' => 'No autorizado'])->assertForbidden();
        $this->get(route('dashboard'))->assertOk()->assertSee('Disponible con un servicio activo y pagado.');
    }
})->with([
    'pendiente' => ['production', 'PENDING', false],
    'rechazado' => ['production', 'DECLINED', false],
    'error' => ['production', 'ERROR', false],
    'anulado' => ['production', 'VOIDED', false],
    'aprobacion verificada simulada' => ['production', 'APPROVED', true],
    'sandbox sin acceso habilitado' => ['sandbox', 'APPROVED', false],
]);
test('admin works view shows plan labels and places clients without membership at the end', function () {
    $admin = createUser('admin', 'Carlos', 'Admin', 'admin-secure@securepapers.test');
    $clientWithoutPlan = createUser('client', 'Ana', 'Sin Plan', 'ana-sin-plan-list@securepapers.test');
    $clientWithPlan = createUser('client', 'Zoe', 'Con Plan', 'zoe-con-plan-list@securepapers.test');

    $planId = createPlan('Correccion Completa');
    createActivePaidSubscription($clientWithPlan->id, $planId);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.works.index'));

    $response
        ->assertOk()
        ->assertSeeInOrder([
            $clientWithPlan->fullName(),
            $clientWithoutPlan->fullName(),
        ])
        ->assertSee('Correccion Completa')
        ->assertSee('Servicio vigente')
        ->assertSee('Sin servicio vigente');
});

function createUser(string $role, string $name, string $lastName, string $email): User
{
    DB::table('users')->insert([
        'role' => $role,
        'name' => $name,
        'last_name' => $lastName,
        'email' => $email,
        'password' => Hash::make('password'),
        'status' => 'active',
        'email_verified_at' => now(),
        'remember_token' => null,
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => null,
    ]);

    return User::query()->where('email', $email)->firstOrFail();
}

function createPlan(string $name): int
{
    return DB::table('payment_plans')->insertGetId([
        'name' => $name,
        'description' => 'Plan de prueba',
        'price' => 100000,
        'duration_days' => 30,
        'max_documents' => 5,
        'includes_meetings' => true,
        'meetings_limit' => 2,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function createActivePaidSubscription(int $userId, int $planId): void
{
    DB::table('user_subscriptions')->insert([
        'user_id' => $userId,
        'payment_plan_id' => $planId,
        'start_date' => now()->subDays(1)->toDateString(),
        'end_date' => now()->addDays(29)->toDateString(),
        'status' => 'active',
        'payment_status' => 'paid',
        'amount_paid' => 100000,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
