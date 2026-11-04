<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserSubscription;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    expect(config('database.default'))->toBe('sqlite')
        ->and(config('database.connections.sqlite.database'))->toBe(':memory:');
    config(['project_notifications.enabled' => false]);
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
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
    Queue::assertNothingPushed();
});

test('paid clients only see their own files notes and team messages', function (string $own, string $other) {
    $this->actingAs($this->{'client'.$own});
    $this->get(route('private.upload-document'))->assertOk()
        ->assertSee('INDICACION-PRIVADA-'.$own)->assertSee('MENSAJE-PRIVADO-'.$own)
        ->assertDontSee('INDICACION-PRIVADA-'.$other)->assertDontSee('MENSAJE-PRIVADO-'.$other)
        ->assertDontSee('EXPEDIENTE-PRIVADO-'.$other);
    foreach ($this->{'documents'.$own} as $document) {
        $this->get(route('documents.file.show', $document))->assertOk();
    }
    foreach ($this->{'documents'.$other} as $document) {
        $this->get(route('documents.file.show', $document))->assertForbidden();
    }
    $this->get(route('admin.works.show', $this->{'client'.$other}))->assertForbidden();
})->with([['A', 'B'], ['B', 'A']]);

test('advisors only list and open their assigned cases and documents', function (string $own, string $other) {
    $this->actingAs($this->{'advisor'.$own});
    $this->get(route('admin.works.index'))->assertOk()
        ->assertSee('EXPEDIENTE-PRIVADO-'.$own)->assertDontSee('EXPEDIENTE-PRIVADO-'.$other);
    $this->get(route('admin.works.show', $this->{'client'.$own}))->assertOk()
        ->assertSee('INDICACION-PRIVADA-'.$own)->assertSee('MENSAJE-PRIVADO-'.$own)
        ->assertDontSee('INDICACION-PRIVADA-'.$other)->assertDontSee('MENSAJE-PRIVADO-'.$other);
    $this->get(route('admin.works.show', $this->{'client'.$other}))->assertForbidden();
    foreach ($this->{'documents'.$own} as $document) {
        $this->get(route('documents.file.show', $document))->assertOk();
    }
    foreach ($this->{'documents'.$other} as $document) {
        $this->get(route('documents.file.show', $document))->assertForbidden();
    }
})->with([['A', 'B'], ['B', 'A']]);

test('unauthorized actors cannot alter another case or send it documents and messages', function (string $actor) {
    $this->actingAs($this->{$actor});
    $this->post(route('admin.works.progress', $this->clientB), [
        'progress_status' => 'delivered',
    ])->assertForbidden();
    $this->post(route('admin.works.store', $this->clientB), ['submission_id' => (string) Str::uuid(),
        'admin_message' => 'INTENTO-NO-AUTORIZADO',
        'admin_files' => [UploadedFile::fake()->create('intrusion.pdf', 1, 'application/pdf')],
    ])->assertForbidden();
    $this->post(route('admin.works.assignment', $this->clientB), [
        'project_title' => 'INTENTO-NO-AUTORIZADO', 'advisor_id' => $this->advisorA->id,
        'progress_status' => 'delivered',
    ])->assertForbidden();
    expect($this->assignmentB->fresh()->only(['advisor_id', 'project_title', 'progress_status']))
        ->toBe($this->assignmentB->only(['advisor_id', 'project_title', 'progress_status']));
    $this->assertDatabaseCount('documents', 4);
    $this->assertDatabaseCount('user_messages', 2);
    $this->assertDatabaseCount('user_activities', 0);
    expect(Storage::disk('local')->allFiles())->toHaveCount(4);
})->with(['advisorA', 'clientA']);

test('a client cannot choose another owner or uploader through extra upload fields', function () {
    $oldOtherNotes = $this->clientB->document_additional_information;
    $this->actingAs($this->clientA)->post(route('private.upload-document.store'), ['submission_id' => (string) Str::uuid(),
        'user_id' => $this->clientB->id, 'uploaded_by' => $this->advisorB->id,
        'additional_information' => 'NOTA-CON-PROPIETARIO-MANIPULADO',
        'document_files' => [UploadedFile::fake()->create('nuevo.pdf', 1, 'application/pdf')],
    ])->assertRedirect(route('private.upload-document'))->assertSessionHasNoErrors();
    $document = Document::where('original_name', 'nuevo.pdf')->sole();
    expect($document->user_id)->toBe($this->clientA->id)
        ->and($document->uploaded_by)->toBe($this->clientA->id)
        ->and($this->clientB->fresh()->document_additional_information)->toBe($oldOtherNotes);
    $this->assertDatabaseCount('documents', 5);
});

test('reassignment revokes the former advisor including files they uploaded', function () {
    $this->actingAs($this->admin)->post(route('admin.works.assignment', $this->clientA), [
        'project_title' => $this->assignmentA->project_title,
        'advisor_id' => $this->advisorB->id, 'progress_status' => 'received',
    ])->assertRedirect(route('admin.works.show', $this->clientA))->assertSessionHasNoErrors();
    expect($this->assignmentA->fresh()->advisor_id)->toBe($this->advisorB->id);
    $this->actingAs($this->advisorA);
    $this->get(route('admin.works.index'))->assertOk()->assertDontSee('EXPEDIENTE-PRIVADO-A');
    $this->get(route('admin.works.show', $this->clientA))->assertForbidden();
    foreach ($this->documentsA as $document) {
        $this->get(route('documents.file.show', $document))->assertForbidden();
    }
    $this->post(route('admin.works.progress', $this->clientA), ['progress_status' => 'delivered'])->assertForbidden();
    $this->post(route('admin.works.store', $this->clientA), ['submission_id' => (string) Str::uuid(), 'admin_message' => 'YA-NO-ASIGNADO'])->assertForbidden();
    $this->actingAs($this->advisorB);
    $this->get(route('admin.works.show', $this->clientA))->assertOk()->assertSee('INDICACION-PRIVADA-A');
    foreach ($this->documentsA as $document) {
        $this->get(route('documents.file.show', $document))->assertOk();
    }
    $this->assertDatabaseCount('user_messages', 2);
    expect($this->assignmentA->fresh()->progress_status)->toBe('received');
});

test('inactive or deleted advisors lose access even with an assignment', function (string $condition) {
    if ($condition === 'inactive') {
        $this->advisorA->forceFill(['status' => 'inactive'])->save();
    } else {
        $this->advisorA->delete();
    }
    $this->actingAs($this->advisorA);
    $this->get(route('admin.works.show', $this->clientA))->assertForbidden();
    foreach ($this->documentsA as $document) {
        $this->get(route('documents.file.show', $document))->assertForbidden();
    }
    $this->post(route('admin.works.progress', $this->clientA), ['progress_status' => 'delivered'])->assertForbidden();
    $this->post(route('admin.works.store', $this->clientA), ['submission_id' => (string) Str::uuid(), 'admin_message' => 'CUENTA-INACTIVA'])->assertForbidden();
    $this->assertDatabaseCount('user_messages', 2);
    expect($this->assignmentA->fresh()->progress_status)->toBe('received');
})->with(['inactive', 'deleted']);

test('assigned advisors can update and return work without modifying another case', function () {
    $this->actingAs($this->advisorA)->post(route('admin.works.progress', $this->clientA), [
        'progress_status' => 'in_review',
    ])->assertRedirect(route('admin.works.show', $this->clientA));
    $this->post(route('admin.works.store', $this->clientA), ['submission_id' => (string) Str::uuid(),
        'admin_message' => 'DEVOLUCION-AUTORIZADA',
        'admin_files' => [UploadedFile::fake()->create('permitido.pdf', 1, 'application/pdf')],
    ])->assertRedirect(route('admin.works.show', $this->clientA))->assertSessionHasNoErrors();
    $document = Document::where('original_name', 'permitido.pdf')->sole();
    expect($document->user_id)->toBe($this->clientA->id)
        ->and($document->uploaded_by)->toBe($this->advisorA->id)
        ->and($this->assignmentA->fresh()->progress_status)->toBe('in_review')
        ->and($this->assignmentB->fresh()->progress_status)->toBe('received');
    $this->assertDatabaseHas('user_messages', [
        'sender_id' => $this->advisorA->id, 'receiver_id' => $this->clientA->id,
        'message' => 'DEVOLUCION-AUTORIZADA',
    ]);
});

test('administrators retain access to both client cases and their files', function () {
    $this->actingAs($this->admin);
    foreach (['A', 'B'] as $side) {
        $this->get(route('admin.works.show', $this->{'client'.$side}))->assertOk()->assertSee('INDICACION-PRIVADA-'.$side);
        foreach ($this->{'documents'.$side} as $document) {
            $this->get(route('documents.file.show', $document))->assertOk();
        }
    }
});

test('guests must sign in before reading files or changing a case', function () {
    $this->get(route('admin.works.show', $this->clientA))->assertRedirect(route('login'));
    $this->get(route('documents.file.show', $this->documentsA[0]))->assertRedirect(route('login'));
    $this->post(route('admin.works.progress', $this->clientA), ['progress_status' => 'delivered'])->assertRedirect(route('login'));
    $this->post(route('admin.works.store', $this->clientA), ['submission_id' => (string) Str::uuid(), 'admin_message' => 'SIN-SESION'])->assertRedirect(route('login'));
    $this->assertDatabaseCount('user_messages', 2);
    expect($this->assignmentA->fresh()->progress_status)->toBe('received');
});

test('case dates use Colombia time without changing stored timestamps', function (string $role) {
    expect(config('app.timezone'))->toBe('UTC');
    $this->clientA->document_additional_information = json_encode([
        ['content' => 'NOTA-SIN-FECHA', 'created_at' => null],
        ['content' => 'NOTA-OFFSET', 'created_at' => '2026-11-03T20:10:00-05:00'],
        ['content' => 'NOTA-UTC', 'created_at' => '2026-11-04T02:15:00+00:00'],
    ]);
    $this->clientA->save();
    $message = UserMessage::where('receiver_id', $this->clientA->id)->firstOrFail();
    $message->update(['sent_at' => '2026-11-04 02:36:00']);
    $document = $this->documentsA[0];
    $document->update(['uploaded_at' => '2026-11-04 02:20:00']);
    $originalNotes = $this->clientA->getRawOriginal('document_additional_information');
    $originalMessage = $message->getRawOriginal('sent_at');
    $originalDocument = $document->getRawOriginal('uploaded_at');

    $actor = match ($role) {
        'client' => $this->clientA,
        'advisor' => $this->advisorA,
        'admin' => $this->admin,
    };
    $url = $role === 'client' ? route('private.upload-document') : route('admin.works.show', $this->clientA);
    $response = $this->actingAs($actor)->get($url)->assertOk()
        ->assertSee('horario de Colombia (UTC−5)')->assertSee('21:36')->assertDontSee('02:36');
    $entries = $response->viewData('additionalInformationEntries');
    expect($entries->pluck('content')->all())->toBe(['NOTA-UTC', 'NOTA-OFFSET', 'NOTA-SIN-FECHA'])
        ->and($entries[0]['date_key'])->toBe('2026-11-03')
        ->and($entries[0]['date_label'])->toBe('03/11/2026')
        ->and($entries[0]['time_label'])->toBe('21:15')
        ->and($entries[1]['time_label'])->toBe('20:10')
        ->and($entries[2]['date_label'])->toBe('Sin fecha')
        ->and($entries[2]['time_label'])->toBeNull();
    if ($role === 'client') {
        $response->assertSee('PDF · 64 B · 03/11/2026');
    }
    expect($this->clientA->fresh()->getRawOriginal('document_additional_information'))->toBe($originalNotes)
        ->and($message->fresh()->getRawOriginal('sent_at'))->toBe($originalMessage)
        ->and($document->fresh()->getRawOriginal('uploaded_at'))->toBe($originalDocument)
        ->and($response->viewData('adminMessages')->first()->sent_at->timezoneName)->toBe('UTC');

    $message->update(['sent_at' => null]);
    $this->get($url)->assertOk()->assertSee('Sin fecha');
})->with(['client', 'advisor', 'admin']);
