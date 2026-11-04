<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserSubscription;
use App\Notifications\AccountPasswordReset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::resetPasswords());
    expect(config('database.default'))->toBe('sqlite')
        ->and(config('database.connections.sqlite.database'))->toBe(':memory:');
    $this->travelTo(Carbon::parse('2030-11-04 12:00:00', 'UTC'));
    config(['project_notifications.enabled' => false]);
    Http::preventStrayRequests();
    Queue::fake();
    Storage::fake('local');
    Storage::fake('recovery-mail');
    config(['mail.default' => 'log', 'mail.mailers.log.channel' => 'recovery_test',
        'logging.channels.recovery_test' => ['driver' => 'single',
            'path' => Storage::disk('recovery-mail')->path('recovery.log'), 'level' => 'debug'],
    ]);
    $this->requestRecoveryLink = function (User $user): string {
        Notification::fake();
        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHasNoErrors()->assertSessionHas('status', __('passwords.sent'));
        Notification::assertSentToTimes($user, AccountPasswordReset::class, 1);

        return Notification::sent($user, AccountPasswordReset::class)->sole()->token;
    };
    $this->resetPayload = static fn (User $user, string $token): array => [
        'email' => $user->email, 'token' => $token,
        'password' => 'Prueba-Recuperacion-2030!', 'password_confirmation' => 'Prueba-Recuperacion-2030!',
    ];
});

afterEach(function () {
    $this->travelBack();
    Http::assertNothingSent();
    Queue::assertNothingPushed();
});

test('the branded recovery email renders through the local log transport and its link works', function () {
    $user = User::factory()->create(['email' => 'recuperacion@propaps.test', 'role' => 'client', 'status' => 'active']);
    $originalPassword = $user->getRawOriginal('password');
    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHasNoErrors()->assertSessionHas('status', __('passwords.sent'));
    $log = quoted_printable_decode(Storage::disk('recovery-mail')->get('recovery.log'));
    expect($log)->toContain('Crear una nueva contraseña')
        ->toContain('Este enlace vence en 60 minutos y solo puede utilizarse una vez.');
    preg_match('~http://127\.0\.0\.1:8000/reset-password/([a-zA-Z0-9]+)\?email=([^\s"<>]+)~', $log, $matches);
    expect($matches)->not->toBeEmpty();
    $token = $matches[1];
    $url = route('password.reset', ['token' => $token, 'email' => $user->email]);
    $this->get($url)->assertOk()->assertSee('Crea una nueva contraseña')->assertSee($user->email);
    expect($user->fresh()->getRawOriginal('password'))->toBe($originalPassword);
    $this->post(route('password.update'), ($this->resetPayload)($user, $token))
        ->assertSessionHasNoErrors()->assertRedirect(route('login', absolute: false));
    expect(Hash::check('Prueba-Recuperacion-2030!', $user->fresh()->password))->toBeTrue();
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
});

test('expired invalid reused or other account links cannot change a password', function (string $scenario) {
    $user = User::factory()->create(['role' => 'client', 'status' => 'active']);
    $token = ($this->requestRecoveryLink)($user);
    if ($scenario === 'expired') {
        $this->travel((int) config('auth.passwords.users.expire'))->minutes();
        $this->travel(1)->seconds();
    } elseif ($scenario === 'invalid') {
        $token = str_repeat('0', 64);
    } elseif ($scenario === 'other account') {
        $user = User::factory()->create(['role' => 'client', 'status' => 'active']);
    } elseif ($scenario === 'reused') {
        $this->post(route('password.update'), ($this->resetPayload)($user, $token))->assertSessionHasNoErrors();
    }
    $originalPassword = $user->fresh()->getRawOriginal('password');
    $payload = ($this->resetPayload)($user, $token);
    $payload['password'] = $payload['password_confirmation'] = 'Otra-Prueba-Segura-2030!';
    $this->post(route('password.update'), $payload)->assertSessionHasErrors('email');
    expect($user->fresh()->getRawOriginal('password'))->toBe($originalPassword);
})->with(['expired', 'invalid', 'reused', 'other account']);

test('request throttling preserves the current link and a later request replaces it', function () {
    $user = User::factory()->create();
    $first = ($this->requestRecoveryLink)($user);
    $stored = DB::table('password_reset_tokens')->where('email', $user->email)->value('token');
    $this->post(route('password.email'), ['email' => $user->email])->assertSessionHasErrors('email');
    Notification::assertSentToTimes($user, AccountPasswordReset::class, 1);
    expect(DB::table('password_reset_tokens')->where('email', $user->email)->value('token'))->toBe($stored);
    $this->travel((int) config('auth.passwords.users.throttle') + 1)->seconds();
    $second = ($this->requestRecoveryLink)($user);
    $this->post(route('password.update'), ($this->resetPayload)($user, $first))->assertSessionHasErrors('email');
    $this->post(route('password.update'), ($this->resetPayload)($user, $second))->assertSessionHasNoErrors();
});

test('mismatched confirmation preserves the password and the recovery link remains usable', function () {
    $user = User::factory()->create();
    $original = $user->getRawOriginal('password');
    $token = ($this->requestRecoveryLink)($user);
    $bad = ($this->resetPayload)($user, $token);
    $bad['password_confirmation'] = 'Una-confirmacion-distinta';
    $this->post(route('password.update'), $bad)->assertSessionHasErrors('password');
    expect($user->fresh()->getRawOriginal('password'))->toBe($original);
    $this->post(route('password.update'), ($this->resetPayload)($user, $token))->assertSessionHasNoErrors();
});

test('recovering access preserves the client case and active or read only permissions', function (bool $active) {
    $client = User::factory()->create(['role' => 'client', 'status' => 'active', 'password' => 'Anterior-Prueba-2030!']);
    $advisor = User::factory()->create(['role' => 'advisor', 'status' => 'active']);
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    $client->appendAdditionalInformation('INDICACIONES CONSERVADAS TRAS RECUPERAR');
    $client->save();
    $plan = PaymentPlan::create(['name' => 'PRUEBA RECUPERACION SIN COBRO', 'price' => 0,
        'duration_days' => 30, 'max_documents' => 10, 'status' => 'active']);
    UserSubscription::create(['user_id' => $client->id, 'payment_plan_id' => $plan->id,
        'start_date' => '2030-10-05', 'end_date' => $active ? '2030-12-03' : '2030-11-03',
        'status' => 'active', 'payment_status' => 'paid', 'amount_paid' => 0]);
    ProjectAssignment::create(['client_id' => $client->id, 'advisor_id' => $advisor->id,
        'assigned_by' => $admin->id, 'project_title' => 'EXPEDIENTE CONSERVADO TRAS RECUPERAR', 'progress_status' => 'delivered']);
    UserMessage::create(['sender_id' => $advisor->id, 'receiver_id' => $client->id,
        'subject' => 'Prueba', 'message' => 'MENSAJE CONSERVADO TRAS RECUPERAR', 'status' => 'sent', 'sent_at' => now()]);
    $type = DocumentType::create(['name' => 'PDF']);
    $documents = [];
    foreach ([$client, $advisor] as $uploader) {
        $path = 'documents/user-'.$client->id.'/prueba-'.$uploader->id.'.pdf';
        Storage::disk('local')->put($path, 'PDF FICTICIO CONSERVADO');
        $documents[] = Document::create(['user_id' => $client->id, 'uploaded_by' => $uploader->id,
            'document_type_id' => $type->id, 'title' => 'Prueba', 'original_name' => basename($path),
            'file_path' => $path, 'mime_type' => 'application/pdf', 'file_size' => 22, 'uploaded_at' => now(),
            'status' => $uploader->id === $client->id ? 'uploaded' : 'shared_by_admin']);
    }
    $caseState = static function () use ($client): array {
        $state = ['user' => $client->fresh()->getAttributes()];
        unset($state['user']['password'], $state['user']['remember_token'], $state['user']['updated_at']);
        foreach (['documents', 'user_messages', 'user_subscriptions', 'project_assignments'] as $table) {
            $state[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        foreach (Storage::disk('local')->allFiles() as $path) {
            $state['files'][$path] = hash('sha256', Storage::disk('local')->get($path));
        }

        return $state;
    };
    $before = $caseState();
    $oldRememberToken = $client->getRememberToken();
    $token = ($this->requestRecoveryLink)($client);
    $this->post(route('password.update'), ($this->resetPayload)($client, $token))
        ->assertSessionHasNoErrors()->assertRedirect(route('login', absolute: false));
    expect($client->fresh()->getRememberToken())->not->toBe($oldRememberToken);
    $this->assertGuest();
    $this->post(route('login'), ['email' => $client->email, 'password' => 'Anterior-Prueba-2030!'])->assertSessionHasErrors('email');
    $this->assertGuest();
    $this->post(route('login'), ['email' => $client->email, 'password' => 'Prueba-Recuperacion-2030!'])->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($client);
    $this->get(route('private.upload-document'))->assertOk()->assertViewHas('canUpload', $active)
        ->assertSee('INDICACIONES CONSERVADAS TRAS RECUPERAR')->assertSee('MENSAJE CONSERVADO TRAS RECUPERAR');
    foreach ($documents as $document) {
        $this->get(route('documents.file.show', $document))->assertOk();
    }
    $this->get(route('admin.works.show', $client))->assertForbidden();
    if (! $active) {
        $this->post(route('private.upload-document.store'), ['additional_information' => 'NO AUTORIZADO'])->assertRedirect(route('private.planes'));
    }
    expect($caseState())->toBe($before);
})->with([true, false]);
