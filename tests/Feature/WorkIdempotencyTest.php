<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserSubscription;
use App\Services\DocumentTypeResolver;
use App\Services\ProjectEmailNotices;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');
    config(['project_notifications.enabled' => false]);
    Http::preventStrayRequests();
    Mail::fake();
    Notification::fake();
    Queue::fake();
    $this->disk = Storage::fake('local');
    $this->client = User::factory()->create(['role' => 'client', 'status' => 'active']);
    $this->advisor = User::factory()->create(['role' => 'advisor', 'status' => 'active']);
    $this->assignment = ProjectAssignment::create(['client_id' => $this->client->id, 'advisor_id' => $this->advisor->id,
        'project_title' => 'EXPEDIENTE FICTICIO', 'progress_status' => 'delivered']);
    $plan = PaymentPlan::create(['name' => 'PRUEBA SIN COBRO', 'price' => 0, 'duration_days' => 30, 'status' => 'active']);
    UserSubscription::create(['user_id' => $this->client->id, 'payment_plan_id' => $plan->id,
        'start_date' => now('America/Bogota')->subDay(), 'end_date' => now('America/Bogota')->addDays(29),
        'status' => 'active', 'payment_status' => 'paid', 'amount_paid' => 0]);
    $type = DocumentType::create(['name' => 'PDF']);
    $this->disk->put('documents/anterior.pdf', 'PDF ANTERIOR FICTICIO');
    Document::create(['user_id' => $this->client->id, 'uploaded_by' => $this->client->id,
        'document_type_id' => $type->id, 'title' => 'Anterior', 'original_name' => 'anterior.pdf',
        'file_path' => 'documents/anterior.pdf', 'mime_type' => 'application/pdf', 'file_size' => 20, 'uploaded_at' => now()]);
    UserMessage::create(['sender_id' => $this->advisor->id, 'receiver_id' => $this->client->id,
        'message' => 'MENSAJE ANTERIOR', 'subject' => 'Prueba', 'status' => 'sent', 'sent_at' => now()]);
    $this->snapshot = function () {
        $state = [];
        foreach (['users', 'documents', 'document_types', 'document_submissions', 'work_submissions', 'user_messages', 'project_assignments', 'user_subscriptions', 'payments', 'wompi_orders'] as $table) {
            $state[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        foreach ($this->disk->allFiles() as $file) {
            $state[$file] = hash('sha256', $this->disk->get($file));
        }

        return $state;
    };
    $this->actingAs($this->advisor)->from(route('admin.works.show', $this->client));
});
afterEach(function () {
    Http::assertNothingSent();
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
    Queue::assertNothingPushed();
});

test('a repeated return saves one copy and remains visible to the client', function (string $kind, string $role) {
    if ($role === 'admin') {
        $this->advisor->forceFill(['role' => 'admin'])->save();
    }
    $key = (string) Str::uuid();
    $payload = fn () => ['submission_id' => $key, 'admin_message' => $kind === 'files' ? null : 'DEVOLUCION UNICA',
        'admin_files' => $kind === 'notes' ? [] : [UploadedFile::fake()->createWithContent('devolucion.pdf', "%PDF-1.4\nFICTICIO")]];
    $this->post(route('admin.works.store', $this->client), $payload())->assertSessionHasNoErrors()->assertSessionHas('work_saved', true);
    $first = ($this->snapshot)();
    $this->post(route('admin.works.store', $this->client), $payload())
        ->assertSessionHas('status', 'Esta devolución ya se había compartido. No se duplicaron documentos ni mensajes.');
    expect(($this->snapshot)())->toBe($first);
    $this->assertDatabaseCount('work_submissions', 1);
    $this->assertDatabaseCount('documents', $kind === 'notes' ? 1 : 2);
    $this->assertDatabaseCount('user_messages', $kind === 'files' ? 1 : 2);
    expect($this->disk->get('documents/anterior.pdf'))->toBe('PDF ANTERIOR FICTICIO')
        ->and($this->assignment->fresh()->progress_status)->toBe('delivered');
    $this->actingAs($this->client);
    $page = $this->get(route('private.upload-document'))->assertOk()->assertSee('MENSAJE ANTERIOR');
    if ($kind !== 'files') {
        $page->assertSee('DEVOLUCION UNICA');
    }
    if ($kind !== 'notes') {
        $page->assertSee('devolucion.pdf');
        $this->get(route('documents.file.show', Document::where('original_name', 'devolucion.pdf')->sole()))->assertOk();
    }
})->with(['notes', 'files', 'both'])->with(['advisor', 'admin']);

test('changed content under an existing return identifier is rejected and preserves the message', function (string $change) {
    $key = (string) Str::uuid();
    $payload = fn ($text, $bytes) => ['submission_id' => $key, 'admin_message' => $text,
        'admin_files' => [UploadedFile::fake()->createWithContent('igual.pdf', "%PDF-1.4\n".$bytes)]];
    $this->post(route('admin.works.store', $this->client), $payload('ORIGINAL', 'original'))->assertSessionHasNoErrors();
    $first = ($this->snapshot)();
    $text = $change === 'message' ? 'EDITADO' : 'ORIGINAL';
    $this->post(route('admin.works.store', $this->client), $payload($text, $change === 'file' ? 'distinto' : 'original'))
        ->assertSessionHasErrors('submission_id')->assertSessionHasInput('admin_message', $text);
    expect(($this->snapshot)())->toBe($first);
    $this->get(route('admin.works.show', $this->client))->assertOk()->assertViewHas('submissionId', fn ($id) => $id !== $key && Str::isUuid($id));
})->with(['message', 'file']);

test('a partial return failure rolls back files message and receipt and allows the same retry', function () {
    $key = (string) Str::uuid();
    $payload = fn () => ['submission_id' => $key, 'admin_message' => 'REINTENTABLE',
        'admin_files' => [UploadedFile::fake()->create('uno.pdf', 1, 'application/pdf'), UploadedFile::fake()->create('dos.pdf', 1, 'application/pdf')]];
    $before = ($this->snapshot)();
    $resolver = app(DocumentTypeResolver::class);
    $this->mock(DocumentTypeResolver::class, function ($mock) use ($resolver) {
        $calls = 0;
        $mock->shouldReceive('resolveId')->andReturnUsing(function ($extension) use (&$calls, $resolver) {
            if (++$calls === 2) {
                throw new RuntimeException('Fallo ficticio del segundo archivo');
            }

            return $resolver->resolveId($extension);
        });
    });
    $this->post(route('admin.works.store', $this->client), $payload())->assertSessionHasErrors('admin_files')
        ->assertSessionHasInput('admin_message', 'REINTENTABLE')->assertSessionHasInput('submission_id', $key);
    expect(($this->snapshot)())->toBe($before);
    app()->instance(DocumentTypeResolver::class, $resolver);
    $this->get(route('admin.works.show', $this->client))->assertViewHas('submissionId', $key);
    $this->post(route('admin.works.store', $this->client), $payload())->assertSessionHasNoErrors();
    $first = ($this->snapshot)();
    $this->post(route('admin.works.store', $this->client), $payload())->assertSessionHas('work_saved', true);
    expect(($this->snapshot)())->toBe($first);
    $this->assertDatabaseCount('documents', 3);
    $this->assertDatabaseCount('user_messages', 2);
});

test('return identifiers must be valid and cannot bypass protection', function ($key) {
    $before = ($this->snapshot)();
    $this->post(route('admin.works.store', $this->client), ['submission_id' => $key, 'admin_message' => 'BORRADOR'])
        ->assertSessionHasErrors('submission_id')->assertSessionHasInput('admin_message', 'BORRADOR');
    expect(($this->snapshot)())->toBe($before);
    $this->get(route('admin.works.show', $this->client))->assertOk()->assertViewHas('submissionId', fn ($id) => Str::isUuid($id));
})->with([[null], ['invalido'], [['lista']]]);

test('a new return may intentionally repeat the same content', function () {
    foreach (range(1, 2) as $attempt) {
        $this->post(route('admin.works.store', $this->client), [
            'submission_id' => (string) Str::uuid(), 'admin_message' => 'INTENCIONAL',
        ])->assertSessionHasNoErrors();
    }
    $this->assertDatabaseCount('work_submissions', 2);
    $this->assertDatabaseCount('user_messages', 3);
});

test('a return receipt never grants access to an advisor whose assignment was revoked', function () {
    $payload = ['submission_id' => (string) Str::uuid(), 'admin_message' => 'RECIBIDO'];
    $this->post(route('admin.works.store', $this->client), $payload)->assertSessionHasNoErrors();
    $this->assignment->update(['advisor_id' => null]);
    $before = ($this->snapshot)();
    $this->post(route('admin.works.store', $this->client), $payload)->assertForbidden();
    expect(($this->snapshot)())->toBe($before);
});

test('return identifiers are isolated by sender and client', function (string $scope) {
    $payload = ['submission_id' => (string) Str::uuid(), 'admin_message' => 'MENSAJE'];
    $this->post(route('admin.works.store', $this->client), $payload)->assertSessionHasNoErrors();
    $target = $this->client;
    if ($scope === 'sender') {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'active']));
    } else {
        $target = User::factory()->create(['role' => 'client', 'status' => 'active']);
        ProjectAssignment::create(['client_id' => $target->id, 'advisor_id' => $this->advisor->id, 'project_title' => 'OTRO', 'progress_status' => 'received']);
    }
    $this->post(route('admin.works.store', $target), $payload)->assertSessionHasNoErrors();
    $this->assertDatabaseCount('work_submissions', 2);
    $this->assertDatabaseCount('user_messages', 3);
})->with(['sender', 'client']);

test('a repeated return schedules only one simulated notice', function () {
    config(['project_notifications.enabled' => true]);
    $this->mock(ProjectEmailNotices::class)->shouldReceive('schedule')->once()->with($this->client->id, 'return');
    $payload = ['submission_id' => (string) Str::uuid(), 'admin_message' => 'AVISO FICTICIO'];
    $this->post(route('admin.works.store', $this->client), $payload)->assertSessionHasNoErrors();
    $this->post(route('admin.works.store', $this->client), $payload)->assertSessionHasNoErrors();
});

test('a form from another staff account cannot send its draft after switching users', function () {
    $before = ($this->snapshot)();
    $this->post(route('admin.works.store', $this->client), ['submission_id' => (string) Str::uuid(),
        'draft_owner' => $this->client->id, 'admin_message' => 'BORRADOR AJENO'])->assertForbidden()->assertSessionMissing('_old_input');
    expect(($this->snapshot)())->toBe($before);
});
