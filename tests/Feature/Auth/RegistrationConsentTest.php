<?php

use App\Models\User;
use App\Notifications\AccountEmailVerification;
use App\Services\LegalConsent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Laravel\Fortify\Features;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleIdentity;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
    expect(config('database.default'))->toBe('sqlite')
        ->and(config('database.connections.sqlite.database'))->toBe(':memory:');
    config(['accounts.email_verification_required' => false, 'project_notifications.enabled' => false]);
    Mail::fake();
    Notification::fake();
    Queue::fake();
    Http::preventStrayRequests();
    $this->registration = [
        'name' => 'Cliente Registro Ficticio', 'email' => 'registro-ficticio@propaps.test',
        'password' => 'Prueba-Registro-2030!', 'password_confirmation' => 'Prueba-Registro-2030!', 'terms' => '1',
    ];
});

afterEach(function () {
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Queue::assertNothingPushed();
    Http::assertNothingSent();
});

test('registration shows an unchecked required consent with accessible terms and privacy links', function () {
    $response = $this->get(route('register'))->assertOk();
    $dom = new DOMDocument;
    @$dom->loadHTML($response->getContent());
    $xpath = new DOMXPath($dom);
    $checkbox = $xpath->query('//input[@name="terms"]')->item(0);
    expect($checkbox)->not->toBeNull()
        ->and($checkbox->hasAttribute('checked'))->toBeFalse()
        ->and($checkbox->hasAttribute('required'))->toBeTrue();
    foreach (['legal.terms', 'legal.privacy'] as $route) {
        $response->assertSee(route($route), false);
        $this->get(route($route))->assertOk();
    }
});

test('registration without affirmative consent creates no account or acceptance', function (mixed $terms) {
    $input = $this->registration;
    if ($terms === null) {
        unset($input['terms']);
    } else {
        $input['terms'] = $terms;
    }
    $this->from(route('register'))->post(route('register.store'), $input)->assertSessionHasErrors([
        'terms' => 'Debes aceptar los términos y la política de privacidad.',
    ]);
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('user_activities', 0);
    Notification::assertNothingSent();
})->with([null, false, '0', 'no']);

test('registration records server chosen consent and creates only a client without paid access', function () {
    $input = array_merge($this->registration, [
        'email' => 'REGISTRO-FICTICIO@PROPAPS.TEST', 'role' => 'admin', 'status' => 'inactive',
        'email_verified_at' => now()->toIso8601String(), 'google_id' => 'INVENTADO',
        'terms_version' => 'VERSION INVENTADA', 'registration_method' => 'admin',
        'payment_status' => 'paid', 'amount_paid' => 999, 'user_id' => 100,
    ]);
    $this->post(route('register.store'), $input)->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));
    $user = User::sole();
    $this->assertAuthenticatedAs($user);
    expect($user->role)->toBe('client')->and($user->status)->toBe('active')
        ->and($user->email)->toBe($this->registration['email'])
        ->and($user->email_verified_at)->toBeNull()->and($user->google_id)->toBeNull()
        ->and(Hash::check($this->registration['password'], $user->password))->toBeTrue();
    $acceptance = DB::table('user_activities')->sole();
    expect($acceptance->user_id)->toBe($user->id)
        ->and($acceptance->action)->toBe('legal_terms_accepted')
        ->and($acceptance->module)->toBe('accounts')
        ->and($acceptance->created_at)->not->toBeNull()
        ->and(json_decode($acceptance->description, true))->toBe([
            'terms_version' => '2026-10-04', 'privacy_version' => '2026-10-04', 'registration_method' => 'password',
        ]);
    foreach (['user_subscriptions', 'payments', 'wompi_orders', 'documents', 'project_assignments'] as $table) {
        $this->assertDatabaseCount($table, 0);
    }
    $this->get(route('dashboard'))->assertOk()->assertSee('Elige cómo empezar');
    $this->get(route('private.upload-document'))->assertRedirect(route('private.planes'));
    $this->postJson(route('private.upload-document.store'), ['submission_id' => (string) Str::uuid(), 'additional_information' => 'SIN SERVICIO'])->assertForbidden();
    $this->get(route('admin.users.index'))->assertForbidden();
    Notification::assertNothingSent();
    $this->post(route('logout'));
    $this->post(route('login'), ['email' => $user->email, 'password' => $this->registration['password']])->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseCount('user_activities', 1);
});

test('invalid registration fields do not create an account or record acceptance', function (string $field, string $value, string $error) {
    $input = $this->registration;
    $input[$field] = $value;
    $this->post(route('register.store'), $input)->assertSessionHasErrors($error);
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('user_activities', 0);
    $this->assertGuest();
    Notification::assertNothingSent();
})->with([
    ['name', '', 'name'],
    ['email', 'correo-invalido', 'email'],
    ['password_confirmation', 'Otra-Prueba-2030!', 'password'],
    ['password', 'abc', 'password'],
]);

test('duplicate registration cannot overwrite the existing user or add acceptance', function () {
    $existing = User::factory()->create(['email' => $this->registration['email'], 'role' => 'advisor']);
    $before = $existing->fresh()->getAttributes();
    $this->post(route('register.store'), $this->registration)->assertSessionHasErrors('email');
    expect($existing->fresh()->getAttributes())->toBe($before);
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('user_activities', 0);
    $this->assertGuest();
    Notification::assertNothingSent();
});

test('failure to record acceptance rolls back account creation', function () {
    $this->mock(LegalConsent::class)->shouldReceive('record')->once()->andThrow(new RuntimeException('FALLO FICTICIO DE REGISTRO'));
    $this->withoutExceptionHandling();
    expect(fn () => $this->post(route('register.store'), $this->registration))
        ->toThrow(RuntimeException::class, 'FALLO FICTICIO DE REGISTRO');
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('user_activities', 0);
    $this->assertGuest();
    Notification::assertNothingSent();
});

test('email verification follows configuration without changing membership access', function (bool $required) {
    config(['accounts.email_verification_required' => $required]);
    $this->post(route('register.store'), $this->registration)->assertSessionHasNoErrors();
    $user = User::sole();
    expect($user->email_verified_at)->toBeNull()->and($user->hasActiveMembership())->toBeFalse();
    if ($required) {
        Notification::assertSentToTimes($user, AccountEmailVerification::class, 1);
        $this->get(route('dashboard'))->assertRedirect(route('verification.notice'));
    } else {
        Notification::assertNothingSent();
        $this->get(route('dashboard'))->assertOk();
    }
})->with([true, false]);

test('a simulated new Google identity requires consent and ignores client supplied identity and role', function () {
    $identity = (new GoogleIdentity)->setRaw(['email_verified' => true])->map([
        'id' => 'google-ficticio-2030', 'email' => 'GOOGLE-FICTICIO@PROPAPS.TEST',
        'name' => 'Cliente Google Ficticio', 'avatar' => null,
    ]);
    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->once()->andReturn($identity);
    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
    $this->get(route('google.callback'))->assertRedirect(route('google.confirm'));
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
    $this->get(route('google.confirm'))->assertOk()->assertSee('google-ficticio@propaps.test');
    $this->post(route('google.register'), [])->assertSessionHasErrors('terms');
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('user_activities', 0);
    $this->post(route('google.register'), ['terms' => '1', 'role' => 'admin',
        'email' => 'intruso@propaps.test', 'google_id' => 'OTRA IDENTIDAD'])->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));
    $user = User::sole();
    expect($user->role)->toBe('client')->and($user->email)->toBe('google-ficticio@propaps.test')
        ->and($user->google_id)->toBe('google-ficticio-2030')->and($user->email_verified_at)->not->toBeNull()
        ->and($user->hasActiveMembership())->toBeFalse();
    $this->assertAuthenticatedAs($user);
    $acceptance = DB::table('user_activities')->sole();
    expect(json_decode($acceptance->description, true))->toBe([
        'terms_version' => '2026-10-04', 'privacy_version' => '2026-10-04', 'registration_method' => 'google',
    ]);
    $this->assertDatabaseCount('user_subscriptions', 0);
    Notification::assertNothingSent();
});
