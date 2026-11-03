<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserSubscription;
use App\Services\ClientJourney;
use App\Services\TrmService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    expect(config('database.default'))->toBe('sqlite')
        ->and(config('database.connections.sqlite.database'))->toBe(':memory:')
        ->and(config('app.timezone'))->toBe('UTC');
    Carbon::setTestNow(Carbon::parse('2030-11-04 12:00:00', 'UTC'));
    config(['project_notifications.enabled' => false, 'wompi.environment' => 'sandbox',
        'wompi.public_key' => '', 'wompi.private_key' => '', 'wompi.integrity_secret' => '',
        'wompi.events_secret' => '', 'wompi.sandbox_access' => false]);
    Mail::fake();
    Notification::fake();
    Queue::fake();
    Http::preventStrayRequests();
    Storage::fake('local');
    $this->mock(TrmService::class)->shouldReceive('current')->andReturn([
        'units' => 40000000, 'from' => '2030-11-03', 'until' => '2030-11-04', 'source' => 'PRUEBA FICTICIA',
    ]);

    $this->client = User::factory()->create(['role' => 'client', 'status' => 'active']);
    $this->advisor = User::factory()->create(['role' => 'advisor', 'status' => 'active']);
    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    $this->client->appendAdditionalInformation('INDICACIONES CONSERVADAS', Carbon::parse('2030-11-02 15:00:00', 'UTC'));
    $this->client->save();
    $plan = PaymentPlan::create(['name' => 'PRUEBA VENCIMIENTO SIN COBRO', 'price' => 0,
        'duration_days' => 30, 'max_documents' => 10, 'status' => 'active']);
    $this->subscription = UserSubscription::create([
        'user_id' => $this->client->id, 'payment_plan_id' => $plan->id,
        'start_date' => '2030-10-05', 'end_date' => '2030-11-03',
        'status' => 'active', 'payment_status' => 'paid', 'amount_paid' => 0,
    ]);
    $this->assignment = ProjectAssignment::create([
        'client_id' => $this->client->id, 'advisor_id' => $this->advisor->id,
        'assigned_by' => $this->admin->id, 'project_title' => 'EXPEDIENTE CONSERVADO',
        'progress_status' => 'delivered',
    ]);
    UserMessage::create(['sender_id' => $this->advisor->id, 'receiver_id' => $this->client->id,
        'subject' => 'Prueba', 'message' => 'MENSAJE CONSERVADO', 'status' => 'sent',
        'sent_at' => '2030-11-03 16:00:00']);
    $type = DocumentType::create(['name' => 'PDF']);
    $this->caseDocuments = collect();
    foreach (['manuscrito' => $this->client, 'devolucion' => $this->advisor] as $kind => $uploader) {
        $path = 'documents/user-'.$this->client->id.'/'.$kind.'.pdf';
        Storage::disk('local')->put($path, 'PDF FICTICIO CONSERVADO '.$kind);
        $this->caseDocuments->push(Document::create([
            'user_id' => $this->client->id, 'uploaded_by' => $uploader->id,
            'document_type_id' => $type->id, 'title' => $kind, 'original_name' => $kind.'.pdf',
            'file_path' => $path, 'mime_type' => 'application/pdf', 'file_size' => 30,
            'uploaded_at' => '2030-11-03 16:00:00',
            'status' => $kind === 'manuscrito' ? 'uploaded' : 'shared_by_admin',
        ]));
    }
    $this->caseSnapshot = static function (): array {
        $snapshot = [];
        foreach (['users', 'documents', 'user_messages', 'project_assignments', 'user_subscriptions', 'payments', 'wompi_orders', 'user_activities'] as $table) {
            $snapshot[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        foreach (Storage::disk('local')->allFiles() as $path) {
            $snapshot['files'][$path] = hash('sha256', Storage::disk('local')->get($path));
        }

        return $snapshot;
    };
});

afterEach(function () {
    Carbon::setTestNow();
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
    Queue::assertNothingPushed();
    Http::assertNothingSent();
});

// Caracteriza la regla UTC actual; no aprueba que el cierre deba ocurrir a las 19:00 de Colombia.
test('current expiry uses the UTC date including the last UTC second', function (string $instant, bool $active, string $colombiaTime) {
    Carbon::setTestNow(Carbon::parse($instant, 'UTC'));
    expect(now('America/Bogota')->format('Y-m-d H:i:s'))->toBe($colombiaTime)
        ->and($this->client->fresh()->hasActiveMembership())->toBe($active);
    $response = $this->actingAs($this->client->fresh())->get(route('private.upload-document'));
    $active ? $response->assertOk() : $response->assertRedirect(route('private.planes'));
})->with([
    ['2030-11-03 23:59:59', true, '2030-11-03 18:59:59'],
    ['2030-11-04 00:00:00', false, '2030-11-03 19:00:00'],
    ['2030-11-04 05:00:00', false, '2030-11-04 00:00:00'],
]);

test('expired clients can view guidance and plans but cannot read the case or send files and notes', function () {
    $before = ($this->caseSnapshot)();
    $this->actingAs($this->client->fresh())->get(route('dashboard'))->assertOk()
        ->assertSee('Período de carga finalizado')->assertSee('Consulta las condiciones de tu servicio');
    expect(app(ClientJourney::class)->forUser($this->client->fresh())['url'])->toBe(route('private.planes'));
    $this->get(route('private.upload-document'))->assertRedirect(route('private.planes'))->assertSessionHas('membership_error');
    $this->get(route('private.planes'))->assertOk()->assertSee('Contrata tu servicio')
        ->assertSee('Necesitas una membresia activa y pagada');
    foreach ($this->caseDocuments as $document) {
        $this->get(route('documents.file.show', $document))->assertForbidden();
    }
    $this->post(route('private.upload-document.store'), ['additional_information' => 'NOTA BLOQUEADA'])
        ->assertRedirect(route('private.planes'));
    $this->post(route('private.upload-document.store'), [
        'document_files' => [UploadedFile::fake()->create('bloqueado.pdf', 1, 'application/pdf')],
    ])->assertRedirect(route('private.planes'));
    $this->postJson(route('private.upload-document.store'), ['additional_information' => 'JSON BLOQUEADO'])->assertForbidden();
    expect(($this->caseSnapshot)())->toBe($before);
});

test('the assigned team can still read an expired client case without changing it', function (string $role) {
    $before = ($this->caseSnapshot)();
    $this->actingAs($this->{$role})->get(route('admin.works.show', $this->client))->assertOk()
        ->assertSee('INDICACIONES CONSERVADAS')->assertSee('MENSAJE CONSERVADO')->assertSee('EXPEDIENTE CONSERVADO');
    foreach ($this->caseDocuments as $document) {
        $this->get(route('documents.file.show', $document))->assertOk();
    }
    expect(($this->caseSnapshot)())->toBe($before);
})->with(['advisor', 'admin']);

test('a synthetic renewed service restores access to the same case without payment or duplicate documents', function () {
    $before = ($this->caseSnapshot)();
    $this->actingAs($this->client->fresh())->get(route('private.upload-document'))->assertRedirect(route('private.planes'));
    $renewal = $this->subscription->replicate();
    $renewal->fill(['start_date' => '2030-11-04', 'end_date' => '2030-12-03', 'amount_paid' => 0])->save();
    $this->actingAs($this->client->fresh())->get(route('private.upload-document'))->assertOk()
        ->assertSee('INDICACIONES CONSERVADAS')->assertSee('MENSAJE CONSERVADO')->assertSee('EXPEDIENTE CONSERVADO');
    foreach ($this->caseDocuments as $document) {
        $this->get(route('documents.file.show', $document))->assertOk();
    }
    $after = ($this->caseSnapshot)();
    unset($before['user_subscriptions'], $after['user_subscriptions']);
    expect($after)->toBe($before);
    $this->assertDatabaseCount('user_subscriptions', 2);
});

test('an assigned advisor can still return work after expiry although the client cannot read it yet', function () {
    $oldNotes = $this->client->getRawOriginal('document_additional_information');
    $this->actingAs($this->advisor)->post(route('admin.works.store', $this->client), [
        'admin_message' => 'DEVOLUCION POSTERIOR AL VENCIMIENTO',
        'admin_files' => [UploadedFile::fake()->create('devolucion-posterior.pdf', 1, 'application/pdf')],
    ])->assertRedirect(route('admin.works.show', $this->client))->assertSessionHasNoErrors();
    $returned = Document::where('original_name', 'devolucion-posterior.pdf')->sole();
    expect($returned->user_id)->toBe($this->client->id)
        ->and($this->client->fresh()->getRawOriginal('document_additional_information'))->toBe($oldNotes)
        ->and($this->assignment->fresh()->progress_status)->toBe('delivered');
    $this->assertDatabaseCount('documents', 3);
    $this->assertDatabaseCount('user_messages', 2);
    foreach ($this->caseDocuments as $document) {
        Storage::disk('local')->assertExists($document->file_path);
    }
    $this->actingAs($this->client->fresh())->get(route('documents.file.show', $returned))->assertForbidden();
    $this->get(route('private.upload-document'))->assertRedirect(route('private.planes'));
});
