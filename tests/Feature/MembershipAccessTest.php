<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

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
        ->assertSee('Ir a cargar (bloqueado)');
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
        ->assertDontSee('Ir a cargar (bloqueado)');
});

test('client can request a plan without receiving upload access until payment is approved', function () {
    $client = createUser('client', 'Camila', 'Pendiente', 'camila-plan-pendiente@securepapers.test');
    $planId = createPlan('Esencial');

    $response = $this
        ->actingAs($client)
        ->post(route('private.planes.select', $planId));

    $response
        ->assertRedirect(route('private.planes'))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('user_subscriptions', [
        'user_id' => $client->id,
        'payment_plan_id' => $planId,
        'status' => 'pending',
        'payment_status' => 'pending',
    ]);

    $this
        ->actingAs($client)
        ->get(route('private.upload-document'))
        ->assertRedirect(route('private.planes'));
});

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
        ->assertSee('Sin membresia');
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
