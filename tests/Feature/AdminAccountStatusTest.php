<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserSubscription;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class AccountStatusCsrfMiddleware extends PreventRequestForgery
{
    protected function runningUnitTests()
    {
        return false;
    }
}

beforeEach(function () {
    expect(config('database.default'))->toBe('sqlite')
        ->and(config('database.connections.sqlite.database'))->toBe(':memory:');
    config(['project_notifications.enabled' => false]);
    $this->app->bind(PreventRequestForgery::class, AccountStatusCsrfMiddleware::class);
    $this->withSession(['_token' => 'account-status-valid-csrf']);
    $this->caseSnapshot = function () {
        $state = [];
        foreach (['documents', 'document_types', 'document_submissions', 'work_submissions', 'user_messages', 'user_subscriptions', 'payments', 'wompi_orders', 'jobs'] as $table) {
            $state[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        foreach (Storage::disk('local')->allFiles() as $file) {
            $state[$file] = hash('sha256', Storage::disk('local')->get($file));
        }

        return $state;
    };
    Mail::fake();
    Notification::fake();
    Queue::fake();
    Http::preventStrayRequests();
    Storage::fake('local');

    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    $type = DocumentType::create(['name' => 'PDF']);
    $plan = PaymentPlan::create([
        'name' => 'PRUEBA AISLAMIENTO', 'price' => 0, 'duration_days' => 30,
        'max_documents' => 10, 'status' => 'active',
    ]);
    foreach (['A', 'B'] as $side) {
        $client = User::factory()->create([
            'role' => 'client', 'status' => 'active', 'name' => 'Cliente '.$side,
            'email' => 'cliente-'.strtolower($side).'@propaps.test',
        ]);
        $advisor = User::factory()->create([
            'role' => 'advisor', 'status' => 'active', 'name' => 'Asesor '.$side,
            'email' => 'asesor-'.strtolower($side).'@propaps.test',
        ]);
        $client->appendAdditionalInformation('INDICACION-PRIVADA-'.$side);
        $client->save();
        UserSubscription::create([
            'user_id' => $client->id, 'payment_plan_id' => $plan->id,
            'start_date' => today()->subDay(), 'end_date' => today()->addDays(29),
            'status' => 'active', 'payment_status' => 'paid', 'amount_paid' => 0,
        ]);
        $assignment = ProjectAssignment::create([
            'client_id' => $client->id, 'advisor_id' => $advisor->id,
            'assigned_by' => $this->admin->id, 'project_title' => 'EXPEDIENTE-PRIVADO-'.$side,
            'progress_status' => 'received',
        ]);
        UserMessage::create([
            'sender_id' => $advisor->id, 'receiver_id' => $client->id,
            'subject' => 'Prueba', 'message' => 'MENSAJE-PRIVADO-'.$side,
            'status' => 'sent', 'sent_at' => now(),
        ]);
        $documents = [];
        foreach (['manuscrito' => $client, 'devolucion' => $advisor] as $kind => $uploader) {
            $name = $kind.'-PRIVADO-'.$side.'.pdf';
            $path = 'documents/user-'.$client->id.'/'.$name;
            Storage::disk('local')->put($path, 'Contenido ficticio '.$name);
            $documents[] = Document::create([
                'user_id' => $client->id, 'uploaded_by' => $uploader->id,
                'document_type_id' => $type->id, 'title' => $name, 'original_name' => $name,
                'file_path' => $path, 'mime_type' => 'application/pdf', 'file_size' => 64,
                'status' => $kind === 'manuscrito' ? 'uploaded' : 'shared_by_admin',
                'uploaded_at' => now(),
            ]);
        }
        $this->{'client'.$side} = $client;
        $this->{'advisor'.$side} = $advisor;
        $this->{'assignment'.$side} = $assignment;
        $this->{'documents'.$side} = $documents;
    }
});

afterEach(function () {
    Http::assertNothingSent();
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
    Queue::assertNothingPushed();
});

test('opening and cancelling a status confirmation does not change an account', function () {
    $before = ($this->caseSnapshot)();
    $profile = $this->clientA->fresh()->getRawOriginal();
    $this->actingAs($this->admin)->get(route('admin.users.status.edit', ['user' => $this->clientA, 'status' => 'inactive']))
        ->assertOk()->assertSee('Confirmar desactivación')->assertSee($this->clientA->email)->assertSee('Motivo del cambio');
    $this->get(route('admin.users.index'))->assertOk()->assertSee('Últimos cambios de acceso');
    expect($this->clientA->fresh()->getRawOriginal())->toBe($profile)->and(($this->caseSnapshot)())->toBe($before);
    $this->assertDatabaseCount('user_activities', 0);
});

test('an admin can deactivate and reactivate with confirmation audit and no duplicated changes', function (string $role) {
    $target = $role === 'client' ? $this->clientA : $this->advisorA;
    if ($role === 'admin') {
        $target->forceFill(['role' => 'admin'])->save();
    }
    $before = ($this->caseSnapshot)();
    $assignments = DB::table('project_assignments')->orderBy('id')->get()->toJson();
    config(['session.driver' => 'database']);
    $this->withSession(['_token' => 'account-status-valid-csrf']);
    DB::table('sessions')->insert(['id' => 'target-session', 'user_id' => $target->id, 'payload' => base64_encode('prueba'), 'last_activity' => time()]);
    $this->actingAs($this->admin)->from(route('admin.users.status.edit', ['user' => $target, 'status' => 'inactive']));
    $payload = ['_token' => 'account-status-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => null, 'confirmed' => '1', 'reason' => 'PRUEBA LOCAL - Cambio confirmado'];
    $this->post(route('admin.users.status.update', $target), $payload)->assertRedirect(route('admin.users.index'))->assertSessionHasNoErrors();
    expect($target->fresh()->status)->toBe('inactive')->and($target->fresh()->session_epoch)->not->toBeNull();
    $this->assertDatabaseMissing('sessions', ['id' => 'target-session']);
    $audit = DB::table('user_activities')->sole();
    $details = json_decode($audit->description, true);
    expect($audit->user_id)->toBe($this->admin->id)->and($details)->toBe(['target_user_id' => $target->id, 'before' => 'active', 'after' => 'inactive', 'reason' => $payload['reason']]);
    $this->post(route('admin.users.status.update', $target), $payload)->assertSessionHas('status', 'La cuenta ya tenía ese estado. No se registró otro cambio.');
    $this->assertDatabaseCount('user_activities', 1);
    $epoch = $target->fresh()->session_epoch;
    $this->post(route('admin.users.status.update', $target), array_merge($payload, ['status' => 'active', 'expected_status' => 'inactive', 'expected_epoch' => $epoch, 'reason' => 'PRUEBA LOCAL - Reactivacion']))->assertSessionHasNoErrors();
    expect($target->fresh()->status)->toBe('active')->and($target->fresh()->session_epoch)->toBe($epoch);
    $this->assertDatabaseCount('user_activities', 2);
    // Un formulario de antes del ciclo completo no puede volver a desactivar la cuenta.
    $this->post(route('admin.users.status.update', $target), $payload)->assertSessionHasErrors('status');
    expect($target->fresh()->status)->toBe('active')->and(($this->caseSnapshot)())->toBe($before)
        ->and(DB::table('project_assignments')->orderBy('id')->get()->toJson())->toBe($assignments);
    $this->assertDatabaseCount('user_activities', 2);
    $this->get(route('admin.users.index'))->assertOk()->assertSee($payload['reason'])->assertSee('PRUEBA LOCAL - Reactivacion');
})->with(['client', 'advisor', 'admin']);

test('clients and advisors cannot change account status or read its confirmation and audit', function (string $role) {
    $this->actingAs($role === 'client' ? $this->clientA : $this->advisorA);
    $this->get(route('admin.users.index'))->assertForbidden();
    $this->get(route('admin.users.status.edit', ['user' => $this->clientB, 'status' => 'inactive']))->assertForbidden();
    $this->post(route('admin.users.status.update', $this->clientB), ['_token' => 'account-status-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => null, 'confirmed' => 1, 'reason' => 'No autorizado'])->assertForbidden();
    expect($this->clientB->fresh()->status)->toBe('active');
    $this->assertDatabaseCount('user_activities', 0);
})->with(['client', 'advisor']);

test('status changes require confirmation valid input and a reason', function (string $field, $value) {
    $payload = ['_token' => 'account-status-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => null, 'confirmed' => 1, 'reason' => 'PRUEBA'];
    $payload[$field] = $value;
    $before = $this->clientA->fresh()->getRawOriginal();
    $this->actingAs($this->admin)->post(route('admin.users.status.update', $this->clientA), $payload)->assertSessionHasErrors($field);
    expect($this->clientA->fresh()->getRawOriginal())->toBe($before);
    $this->assertDatabaseCount('user_activities', 0);
})->with([['confirmed', null], ['confirmed', '0'], ['reason', '   '], ['reason', str_repeat('a', 501)], ['status', 'deleted'], ['expected_epoch', 'invalid']]);

test('administrators cannot deactivate themselves or the last active administrator', function (bool $another) {
    if ($another) {
        $this->advisorB->forceFill(['role' => 'admin'])->save();
    }
    $this->actingAs($this->admin)->post(route('admin.users.status.update', $this->admin), [
        '_token' => 'account-status-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => null, 'confirmed' => 1, 'reason' => 'PRUEBA',
    ])->assertSessionHasErrors(['status' => $another ? 'No puedes desactivar tu propia cuenta.' : 'No puedes desactivar al último administrador activo.']);
    expect($this->admin->fresh()->status)->toBe('active');
    $this->assertDatabaseCount('user_activities', 0);
})->with([false, true]);

test('an audit failure rolls back status revocation and database session removal', function () {
    config(['session.driver' => 'database']);
    $this->withSession(['_token' => 'account-status-valid-csrf']);
    DB::table('sessions')->insert(['id' => 'preserved', 'user_id' => $this->clientA->id, 'payload' => base64_encode('prueba'), 'last_activity' => time()]);
    $profile = $this->clientA->fresh()->getRawOriginal();
    $before = ($this->caseSnapshot)();
    DB::listen(function ($query) {
        if (str_starts_with(strtolower($query->sql), 'insert into "user_activities"')) {
            throw new RuntimeException('Fallo ficticio de auditoria');
        }
    });
    $this->actingAs($this->admin)->post(route('admin.users.status.update', $this->clientA), [
        '_token' => 'account-status-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => null, 'confirmed' => 1, 'reason' => 'PRUEBA',
    ])->assertStatus(500);
    expect($this->clientA->fresh()->getRawOriginal())->toBe($profile)->and(($this->caseSnapshot)())->toBe($before);
    $this->assertDatabaseHas('sessions', ['id' => 'preserved']);
    $this->assertDatabaseCount('user_activities', 0);
});
