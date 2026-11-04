<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\DocumentTypeResolver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');
    config(['project_notifications.enabled' => false]);
    Http::preventStrayRequests();
    Mail::fake();
    Notification::fake();
    Queue::fake();
    $this->disk = Storage::fake('local');
    $this->client = User::factory()->create(['role' => 'client', 'status' => 'active']);
    $this->client->appendAdditionalInformation('INDICACION ANTERIOR CONSERVADA');
    $this->client->save();
    $plan = PaymentPlan::create(['name' => 'PRUEBA CARGAS SIN COBRO', 'price' => 0,
        'duration_days' => 30, 'max_documents' => 10, 'status' => 'active']);
    UserSubscription::create(['user_id' => $this->client->id, 'payment_plan_id' => $plan->id,
        'start_date' => now('America/Bogota')->subDay()->toDateString(),
        'end_date' => now('America/Bogota')->addDays(29)->toDateString(),
        'status' => 'active', 'payment_status' => 'paid', 'amount_paid' => 0]);
    $type = DocumentType::create(['name' => 'PDF']);
    $this->disk->put('documents/conservado.pdf', 'PDF FICTICIO ANTERIOR');
    Document::create(['user_id' => $this->client->id, 'uploaded_by' => $this->client->id,
        'document_type_id' => $type->id, 'title' => 'conservado', 'original_name' => 'conservado.pdf',
        'file_path' => 'documents/conservado.pdf', 'mime_type' => 'application/pdf',
        'file_size' => 21, 'uploaded_at' => now()]);
    $this->snapshot = function () {
        $result = [];
        foreach (['users', 'documents', 'document_types', 'user_subscriptions', 'user_messages', 'project_assignments', 'payments', 'wompi_orders'] as $table) {
            $result[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        foreach ($this->disk->allFiles() as $path) {
            $result['files'][$path] = hash('sha256', $this->disk->get($path));
        }

        return $result;
    };
    $this->actingAs($this->client)->from(route('private.upload-document'));
});

afterEach(function () {
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
    Queue::assertNothingPushed();
    Http::assertNothingSent();
});

test('invalid uploads preserve the draft and the entire case', function (string $kind, string $key, string $message) {
    $files = match ($kind) {
        'oversized' => [UploadedFile::fake()->create('grande.pdf', 20481, 'application/pdf')],
        'format' => [UploadedFile::fake()->create('prohibido.txt', 1, 'text/plain')],
        'disguised' => [UploadedFile::fake()->create('falso.pdf', 1, 'text/plain')],
        'count' => array_map(fn ($n) => UploadedFile::fake()->create("prueba-$n.pdf", 1, 'application/pdf'), range(1, 11)),
        'mixed' => [UploadedFile::fake()->create('valido.pdf', 1, 'application/pdf'), UploadedFile::fake()->create('prohibido.txt', 1, 'text/plain')],
        default => [],
    };
    $draft = $kind === 'notes' ? str_repeat('a', 8001) : 'BORRADOR FICTICIO QUE DEBE CONSERVARSE';
    $before = ($this->snapshot)();
    $this->post(route('private.upload-document.store'), ['document_files' => $files, 'additional_information' => $draft])
        ->assertRedirect(route('private.upload-document'))
        ->assertSessionHasErrors([$key => $message])
        ->assertSessionHasInput('additional_information', $draft);
    expect(($this->snapshot)())->toBe($before);
    // Renderiza el formulario con los datos de la sesión, sin publicar nada.
    $response = $this->get(route('private.upload-document'));
    $response->assertOk()->assertSee($draft);
})->with([
    ['oversized', 'document_files.0', 'Cada archivo puede pesar maximo 20 MB.'],
    ['format', 'document_files.0', 'Solo puedes cargar archivos PDF, Word, Excel o imagenes.'],
    ['disguised', 'document_files.0', 'Solo puedes cargar archivos PDF, Word, Excel o imagenes.'],
    ['count', 'document_files', 'Puedes adjuntar hasta 10 archivos por envío.'],
    ['mixed', 'document_files.1', 'Solo puedes cargar archivos PDF, Word, Excel o imagenes.'],
    ['notes', 'additional_information', 'Las indicaciones no pueden superar los 8.000 caracteres.'],
]);

test('the exact file count size and note limits are accepted', function () {
    $files = array_map(fn ($n) => UploadedFile::fake()->create("limite-$n.pdf", 20480, 'application/pdf'), range(1, 10));
    $this->post(route('private.upload-document.store'), [
        'document_files' => $files, 'additional_information' => str_repeat('a', 8000),
    ])->assertRedirect(route('private.upload-document'))->assertSessionHasNoErrors();
    $this->assertDatabaseCount('documents', 11);
    expect($this->disk->allFiles())->toHaveCount(11)
        ->and($this->client->fresh()->additionalInformationEntries())->toHaveCount(2);
});

test('an empty submission does not change the case', function () {
    $before = ($this->snapshot)();
    $this->post(route('private.upload-document.store'), [])->assertRedirect(route('private.upload-document'))
        ->assertSessionHasErrors('document_files');
    expect(($this->snapshot)())->toBe($before);
});

test('a failure during a batch removes new files rolls back records and preserves the draft for retry', function (string $failure) {
    $before = ($this->snapshot)();
    if ($failure === 'storage') {
        $disk = $this->disk;
        $calls = 0;
        $proxy = Mockery::mock($disk);
        $proxy->shouldReceive('putFileAs')->twice()->andReturnUsing(function (...$args) use ($disk, &$calls) {
            return ++$calls === 2 ? false : $disk->putFileAs(...$args);
        });
        Storage::set('local', $proxy);
    } else {
        $typeId = DocumentType::where('name', 'PDF')->value('id');
        $this->mock(DocumentTypeResolver::class)->shouldReceive('resolveId')
            ->once()->ordered()->andReturn($typeId);
        app(DocumentTypeResolver::class)->shouldReceive('resolveId')->once()->ordered()
            ->andThrow(new RuntimeException('FALLO INTERNO FICTICIO NO VISIBLE'));
    }
    $draft = 'BORRADOR TRAS FALLO DE GUARDADO';
    $this->post(route('private.upload-document.store'), [
        'document_files' => [UploadedFile::fake()->create('primero.pdf', 1, 'application/pdf'), UploadedFile::fake()->create('segundo.pdf', 1, 'application/pdf')],
        'additional_information' => $draft,
    ])->assertRedirect(route('private.upload-document'))
        ->assertSessionHasErrors(['document_files' => 'No pudimos guardar el envío. Tus indicaciones se conservan; vuelve a seleccionar los archivos e inténtalo de nuevo.'])
        ->assertSessionHasInput('additional_information', $draft);
    expect(($this->snapshot)())->toBe($before);
    $this->get(route('private.upload-document'))->assertOk()->assertSee($draft)->assertDontSee('FALLO INTERNO FICTICIO');
    Storage::set('local', $this->disk);
    $this->app->forgetInstance(DocumentTypeResolver::class);
    app('router')->getRoutes()->getByName('private.upload-document.store')->flushController();
    $this->post(route('private.upload-document.store'), [
        'document_files' => [UploadedFile::fake()->create('reintento.pdf', 1, 'application/pdf')],
        'additional_information' => $draft,
    ])->assertRedirect(route('private.upload-document'))->assertSessionHasNoErrors();
    $this->assertDatabaseCount('documents', 2);
    expect($this->disk->allFiles())->toHaveCount(2)
        ->and($this->client->fresh()->additionalInformationEntries())->toHaveCount(2);
})->with(['storage', 'record']);
