<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can open a client document from the protected document route', function () {
    Storage::fake('local');

    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $document = createProtectedDocumentFor($client);

    $this
        ->actingAs($admin)
        ->get(route('documents.file.show', $document))
        ->assertOk();
});

test('a client cannot open another client document', function () {
    Storage::fake('local');

    $owner = User::factory()->create(['role' => 'client']);
    $otherClient = User::factory()->create(['role' => 'client']);
    createActiveSubscriptionFor($otherClient);
    $document = createProtectedDocumentFor($owner);

    $this
        ->actingAs($otherClient)
        ->get(route('documents.file.show', $document))
        ->assertForbidden();
});

test('a client with active membership can open their own document', function () {
    Storage::fake('local');

    $client = User::factory()->create(['role' => 'client']);
    createActiveSubscriptionFor($client);
    $document = createProtectedDocumentFor($client);

    $this
        ->actingAs($client)
        ->get(route('documents.file.show', $document))
        ->assertOk();
});

test('a client with active membership can upload a document', function () {
    Storage::fake('local');

    $client = User::factory()->create(['role' => 'client']);
    createActiveSubscriptionFor($client);

    $file = UploadedFile::fake()->create('paper.pdf', 128, 'application/pdf');

    $this
        ->actingAs($client)
        ->post(route('private.upload-document.store'), [
            'document_files' => [$file],
        ])
        ->assertRedirect(route('private.upload-document'))
        ->assertSessionHas('status', 'Archivo cargado correctamente.');

    $document = Document::query()->where('user_id', $client->id)->first();

    expect($document)->not->toBeNull()
        ->and($document->uploaded_by)->toBe($client->id)
        ->and($document->original_name)->toBe('paper.pdf');

    Storage::disk('local')->assertExists($document->file_path);
});

test('an admin can upload a document for a client', function () {
    Storage::fake('local');

    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);

    $file = UploadedFile::fake()->create('revision.docx', 128, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

    $this
        ->actingAs($admin)
        ->post(route('admin.works.store', $client), [
            'admin_files' => [$file],
        ])
        ->assertRedirect(route('admin.works.show', $client))
        ->assertSessionHas('status', 'Archivo enviado al usuario correctamente.');

    $document = Document::query()->where('user_id', $client->id)->first();

    expect($document)->not->toBeNull()
        ->and($document->uploaded_by)->toBe($admin->id)
        ->and($document->original_name)->toBe('revision.docx')
        ->and($document->status)->toBe('shared_by_admin');

    Storage::disk('local')->assertExists($document->file_path);
});

test('a client without active membership cannot open their own document directly', function () {
    Storage::fake('local');

    $client = User::factory()->create(['role' => 'client']);
    $document = createProtectedDocumentFor($client);

    $this
        ->actingAs($client)
        ->get(route('documents.file.show', $document))
        ->assertForbidden();
});

function createProtectedDocumentFor(User $user): Document
{
    $path = 'documents/user-'.$user->id.'/example.pdf';

    Storage::disk('local')->put($path, 'fake-pdf-content');

    $documentType = DocumentType::query()->create([
        'name' => 'PDF',
        'description' => 'Documento PDF',
    ]);

    return Document::query()->create([
        'user_id' => $user->id,
        'document_type_id' => $documentType->id,
        'uploaded_by' => $user->id,
        'title' => 'example',
        'file_path' => $path,
        'original_name' => 'example.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 16,
        'uploaded_at' => now(),
    ]);
}

function createActiveSubscriptionFor(User $user): UserSubscription
{
    $plan = PaymentPlan::query()->create([
        'name' => 'Plan '.$user->id,
        'description' => 'Plan de prueba',
        'price' => 100000,
        'duration_days' => 30,
        'max_documents' => 5,
        'includes_meetings' => true,
        'meetings_limit' => 1,
        'status' => 'active',
    ]);

    return UserSubscription::query()->create([
        'user_id' => $user->id,
        'payment_plan_id' => $plan->id,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDays(29)->toDateString(),
        'status' => 'active',
        'payment_status' => 'paid',
        'amount_paid' => 100000,
    ]);
}
