<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\User;
use App\Models\UserSubscription;
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
    $this->client->appendAdditionalInformation('INDICACION ANTERIOR');
    $this->client->save();
    $plan = PaymentPlan::create(['name' => 'PRUEBA DUPLICADOS SIN COBRO', 'price' => 0, 'duration_days' => 30, 'status' => 'active']);
    $this->subscription = UserSubscription::create(['user_id' => $this->client->id, 'payment_plan_id' => $plan->id,
        'start_date' => now('America/Bogota')->subDay(), 'end_date' => now('America/Bogota')->addDays(29),
        'status' => 'active', 'payment_status' => 'paid', 'amount_paid' => 0]);
    $type = DocumentType::create(['name' => 'PDF']);
    $this->disk->put('documents/anterior.pdf', 'PDF FICTICIO ANTERIOR');
    Document::create(['user_id' => $this->client->id, 'uploaded_by' => $this->client->id,
        'document_type_id' => $type->id, 'title' => 'Anterior', 'original_name' => 'anterior.pdf',
        'file_path' => 'documents/anterior.pdf', 'mime_type' => 'application/pdf', 'file_size' => 21, 'uploaded_at' => now()]);
    $this->snapshot = function () {
        $state = [];
        foreach (['users', 'documents', 'document_types', 'document_submissions', 'user_messages', 'project_assignments', 'user_subscriptions', 'payments', 'wompi_orders'] as $table) {
            $state[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        foreach ($this->disk->allFiles() as $file) {
            $state[$file] = hash('sha256', $this->disk->get($file));
        }

        return $state;
    };
    $this->actingAs($this->client)->from(route('private.upload-document'));
});
afterEach(function () {
    Http::assertNothingSent();
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
    Queue::assertNothingPushed();
});

test('repeating the same submission saves exactly one copy', function (string $kind) {
    $key = (string) Str::uuid();
    $payload = fn () => ['submission_id' => $key,
        'additional_information' => $kind === 'files' ? null : 'INDICACION NUEVA',
        'document_files' => $kind === 'notes' ? [] : [UploadedFile::fake()->create('nuevo.pdf', 1, 'application/pdf')]];
    $this->post(route('private.upload-document.store'), $payload())->assertRedirect(route('private.upload-document'))
        ->assertSessionHasNoErrors()->assertSessionHas('upload_saved', true);
    $first = ($this->snapshot)();
    $this->post(route('private.upload-document.store'), $payload())->assertRedirect(route('private.upload-document'))
        ->assertSessionHas('status', 'Este envío ya se había recibido. No se duplicaron archivos ni indicaciones.')
        ->assertSessionHas('upload_saved', true);
    expect(($this->snapshot)())->toBe($first)
        ->and($this->client->fresh()->additionalInformationEntries())->toHaveCount($kind === 'files' ? 1 : 2)
        ->and($this->disk->get('documents/anterior.pdf'))->toBe('PDF FICTICIO ANTERIOR');
    $this->assertDatabaseCount('documents', $kind === 'notes' ? 1 : 2);
    $this->assertDatabaseCount('document_submissions', 1);
})->with(['notes', 'files', 'both']);

test('reusing a received identifier for different content is rejected without losing the draft', function (string $change) {
    $key = (string) Str::uuid();
    $payload = fn ($text, $bytes) => ['submission_id' => $key, 'additional_information' => $text,
        'document_files' => [UploadedFile::fake()->createWithContent('mismo-nombre.pdf', "%PDF-1.4\n".$bytes)]];
    $this->post(route('private.upload-document.store'), $payload('PRIMER TEXTO', 'original'))->assertSessionHasNoErrors();
    $first = ($this->snapshot)();
    $text = $change === 'notes' ? 'TEXTO EDITADO' : 'PRIMER TEXTO';
    $this->post(route('private.upload-document.store'), $payload($text, $change === 'file' ? 'distinto' : 'original'))
        ->assertRedirect(route('private.upload-document'))->assertSessionHasErrors('submission_id')
        ->assertSessionHasInput('additional_information', $text);
    expect(($this->snapshot)())->toBe($first);
})->with(['notes', 'file']);

test('a genuinely new submission may intentionally repeat the same text', function () {
    foreach (range(1, 2) as $attempt) {
        $this->post(route('private.upload-document.store'), [
            'submission_id' => (string) Str::uuid(), 'additional_information' => 'MISMO TEXTO INTENCIONAL',
        ])->assertSessionHasNoErrors();
    }
    $this->assertDatabaseCount('document_submissions', 2);
    expect($this->client->fresh()->additionalInformationEntries())->toHaveCount(3);
});

test('identifiers are scoped to the owner and never expose another account receipt', function () {
    $key = (string) Str::uuid();
    $payload = ['submission_id' => $key, 'additional_information' => 'PRUEBA'];
    $this->post(route('private.upload-document.store'), $payload)->assertSessionHasNoErrors();
    $other = User::factory()->create(['role' => 'client', 'status' => 'active']);
    $subscription = $this->subscription->replicate();
    $subscription->user_id = $other->id;
    $subscription->save();
    $this->actingAs($other)->post(route('private.upload-document.store'), $payload)->assertSessionHasNoErrors();
    $this->assertDatabaseCount('document_submissions', 2);
    expect($other->fresh()->additionalInformationEntries())->toHaveCount(1);
});

test('a failed save rolls back the receipt and the same identifier can be retried', function () {
    $key = (string) Str::uuid();
    $payload = fn () => ['submission_id' => $key, 'additional_information' => 'BORRADOR REINTENTABLE',
        'document_files' => [UploadedFile::fake()->create('reintento.pdf', 1, 'application/pdf')]];
    $before = ($this->snapshot)();
    $proxy = Mockery::mock($this->disk);
    $proxy->shouldReceive('putFileAs')->once()->andReturn(false);
    Storage::set('local', $proxy);
    $this->post(route('private.upload-document.store'), $payload())->assertSessionHasErrors('document_files')
        ->assertSessionHasInput('submission_id', $key);
    expect(($this->snapshot)())->toBe($before);
    Storage::set('local', $this->disk);
    $this->get(route('private.upload-document'))->assertOk()->assertViewHas('submissionId', $key);
    $this->post(route('private.upload-document.store'), $payload())->assertSessionHasNoErrors();
    $first = ($this->snapshot)();
    $this->post(route('private.upload-document.store'), $payload())->assertSessionHas('upload_saved', true);
    expect(($this->snapshot)())->toBe($first);
    $this->assertDatabaseCount('documents', 2);
    $this->assertDatabaseCount('document_submissions', 1);
});

test('missing or malformed identifiers cannot silently create an unprotected submission', function ($key) {
    $before = ($this->snapshot)();
    $this->post(route('private.upload-document.store'), ['submission_id' => $key, 'additional_information' => 'BORRADOR'])
        ->assertSessionHasErrors('submission_id')->assertSessionHasInput('additional_information', 'BORRADOR');
    expect(($this->snapshot)())->toBe($before);
    $this->get(route('private.upload-document'))->assertOk()->assertViewHas('submissionId', fn ($id) => Str::isUuid($id));
})->with([[null], ['no-es-un-uuid'], [['malformed']]]);

test('a replay does not schedule the project notice twice', function () {
    config(['project_notifications.enabled' => true]);
    $this->mock(ProjectEmailNotices::class)->shouldReceive('schedule')->once()->with($this->client->id, 'upload');
    $payload = ['submission_id' => (string) Str::uuid(), 'additional_information' => 'AVISO SIMULADO'];
    $this->post(route('private.upload-document.store'), $payload)->assertSessionHasNoErrors();
    $this->post(route('private.upload-document.store'), $payload)->assertSessionHasNoErrors();
});
