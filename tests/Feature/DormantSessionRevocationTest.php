<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserSubscription;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RevokedSessionCsrfMiddleware extends PreventRequestForgery
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
    $this->app->bind(PreventRequestForgery::class, RevokedSessionCsrfMiddleware::class);
    $this->withSession(['_token' => 'revocation-valid-csrf']);
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

test('a dormant session stays revoked after deactivation and reactivation before its next request', function (string $role, bool $legacy) {
    $actor = match ($role) {
        'client' => $this->clientA, 'advisor' => $this->advisorA, 'admin' => $this->admin
    };
    $this->actingAs($actor);
    if (! $legacy) {
        $this->withSession(['account_session' => ['user_id' => $actor->id, 'epoch' => null]]);
    }
    $this->get(route('dashboard'))->assertOk();
    $before = ($this->caseSnapshot)();
    $oldRemember = $actor->getRememberToken();
    $actor->forceFill(['status' => 'inactive'])->save();
    $epoch = $actor->fresh()->session_epoch;
    expect(Str::isUuid($epoch))->toBeTrue()->and($actor->fresh()->getRememberToken())->not->toBe($oldRemember);
    $actor->forceFill(['status' => 'active'])->save();
    expect($actor->fresh()->session_epoch)->toBe($epoch);
    $this->post(route($role === 'client' ? 'private.upload-document.store' : 'admin.works.store', $role === 'client' ? [] : $this->clientA), [
        '_token' => 'revocation-valid-csrf', 'submission_id' => (string) Str::uuid(), 'draft_owner' => $actor->id,
        'additional_information' => 'BORRADOR REVOCADO', 'admin_message' => 'BORRADOR REVOCADO',
        ($role === 'client' ? 'document_files' : 'admin_files') => [UploadedFile::fake()->create('rechazado.pdf', 1, 'application/pdf')],
    ])->assertForbidden()->assertSee('Esta sesión anterior ya no es válida.')->assertDontSee('BORRADOR REVOCADO')
        ->assertHeader('Cache-Control', 'no-store, private')->assertSessionMissing('_old_input');
    $this->assertGuest();
    expect(($this->caseSnapshot)())->toBe($before);
    $this->get(route('login'))->assertOk();
    $this->post(route('login.store'), ['_token' => session()->token(), 'email' => $actor->email, 'password' => 'password'])->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($actor);
    expect(session('account_session'))->toBe(['user_id' => $actor->id, 'epoch' => $epoch]);
    $this->get(route('dashboard'))->assertOk();
    $this->get(route('documents.file.show', $this->documentsA[0]))->assertOk();
    expect(($this->caseSnapshot)())->toBe($before);
})->with(['client', 'advisor', 'admin'])->with([true, false]);

test('deactivation deletes only that account database sessions and rotates its remember token', function () {
    config(['session.driver' => 'database']);
    foreach (['first', 'second', 'other'] as $id) {
        DB::table('sessions')->insert([
            'id' => $id, 'user_id' => $id === 'other' ? $this->clientB->id : $this->clientA->id,
            'payload' => base64_encode('PRUEBA'), 'last_activity' => time(),
        ]);
    }
    $other = (array) DB::table('sessions')->where('id', 'other')->sole();
    $otherUser = $this->clientB->fresh()->getRawOriginal();
    $remember = $this->clientA->getRememberToken();
    $before = ($this->caseSnapshot)();
    $this->clientA->forceFill(['status' => 'inactive'])->save();
    expect(DB::table('sessions')->where('user_id', $this->clientA->id)->count())->toBe(0)
        ->and((array) DB::table('sessions')->where('id', 'other')->sole())->toBe($other)
        ->and($this->clientB->fresh()->getRawOriginal())->toBe($otherUser)
        ->and($this->clientA->fresh()->getRememberToken())->not->toBe($remember)
        ->and(($this->caseSnapshot)())->toBe($before);
});

test('ordinary profile updates preserve sessions and every later deactivation creates a new revocation', function () {
    $this->clientA->forceFill(['status' => 'inactive'])->save();
    $first = $this->clientA->fresh()->session_epoch;
    $this->clientA->forceFill(['status' => 'active', 'name' => 'Nombre ficticio actualizado'])->save();
    expect($this->clientA->fresh()->session_epoch)->toBe($first);
    $this->clientA->forceFill(['status' => 'inactive'])->save();
    expect($this->clientA->fresh()->session_epoch)->not->toBe($first);
});

test('a stale session cannot rotate the remember token of a fresh login', function () {
    $this->clientA->forceFill(['status' => 'inactive'])->save();
    $this->clientA->forceFill(['status' => 'active'])->save();
    $currentRemember = $this->clientA->fresh()->getRememberToken();
    $this->actingAs($this->clientA)->withSession(['account_session' => ['user_id' => $this->clientA->id, 'epoch' => null]])
        ->getJson(route('dashboard'))->assertForbidden()->assertJson(['message' => 'Esta sesión fue cerrada. Vuelve a ingresar para continuar.']);
    $this->assertGuest();
    expect($this->clientA->fresh()->getRememberToken())->toBe($currentRemember);
});
