<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserSubscription;
use App\Services\ProjectAccess;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReassignmentCsrfMiddleware extends PreventRequestForgery
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
    $this->app->bind(PreventRequestForgery::class, ReassignmentCsrfMiddleware::class);
    $this->withSession(['_token' => 'reassignment-valid-csrf']);
    $this->caseSnapshot = function () {
        $state = [];
        foreach (['users', 'documents', 'document_types', 'document_submissions', 'work_submissions', 'user_messages', 'user_subscriptions', 'payments', 'wompi_orders', 'jobs'] as $table) {
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

test('a form opened before reassignment cannot return work and the successor retains the full case', function (string $kind) {
    $page = $this->actingAs($this->advisorA)->get(route('admin.works.show', $this->clientA))->assertOk();
    $key = $page->viewData('submissionId');
    expect(Str::isUuid($key))->toBeTrue();
    $before = ($this->caseSnapshot)();
    $otherAssignment = $this->assignmentB->fresh()->getRawOriginal();
    $this->actingAs($this->admin)->post(route('admin.works.assignment', $this->clientA), [
        '_token' => 'reassignment-valid-csrf', 'project_title' => $this->assignmentA->project_title,
        'advisor_id' => $this->advisorB->id, 'progress_status' => $this->assignmentA->progress_status,
    ])->assertRedirect(route('admin.works.show', $this->clientA))->assertSessionHasNoErrors();
    expect(($this->caseSnapshot)())->toBe($before)
        ->and($this->assignmentB->fresh()->getRawOriginal())->toBe($otherAssignment);
    $assignment = $this->assignmentA->fresh()->getRawOriginal();
    $activities = DB::table('user_activities')->orderBy('id')->get()->toJson();
    $this->actingAs($this->advisorA)->post(route('admin.works.store', $this->clientA), [
        '_token' => 'reassignment-valid-csrf', 'submission_id' => $key, 'draft_owner' => $this->advisorA->id,
        'admin_message' => $kind === 'files' ? null : 'BORRADOR NO COMPARTIDO',
        'admin_files' => $kind === 'message' ? [] : [UploadedFile::fake()->create('no-compartir.pdf', 1, 'application/pdf')],
    ])->assertForbidden()->assertSee('Este intento no añadió documentos ni mensajes.')
        ->assertSee('Volver a mis proyectos')->assertDontSee('BORRADOR NO COMPARTIDO')
        ->assertDontSee('INDICACION-PRIVADA-A')->assertDontSee('EXPEDIENTE-PRIVADO-A')
        ->assertHeader('Cache-Control', 'no-store, private')->assertSessionMissing('_old_input');
    $this->post(route('admin.works.progress', $this->clientA), ['_token' => 'reassignment-valid-csrf', 'progress_status' => 'delivered'])->assertForbidden();
    $this->get(route('admin.works.show', $this->clientA))->assertForbidden();
    foreach ($this->documentsA as $document) {
        $this->get(route('documents.file.show', $document))->assertForbidden();
    }
    expect(($this->caseSnapshot)())->toBe($before)
        ->and($this->assignmentA->fresh()->getRawOriginal())->toBe($assignment)
        ->and(DB::table('user_activities')->orderBy('id')->get()->toJson())->toBe($activities);
    $this->actingAs($this->advisorB)->get(route('admin.works.show', $this->clientA))->assertOk()
        ->assertSee('INDICACION-PRIVADA-A')->assertSee('MENSAJE-PRIVADO-A');
    foreach ($this->documentsA as $document) {
        $this->get(route('documents.file.show', $document))->assertOk();
    }
    $this->actingAs($this->clientA)->get(route('private.upload-document'))->assertOk()
        ->assertSee('MENSAJE-PRIVADO-A')->assertSee('INDICACION-PRIVADA-A');
    foreach ($this->documentsA as $document) {
        $this->get(route('documents.file.show', $document))->assertOk();
    }
    expect(($this->caseSnapshot)())->toBe($before);
})->with(['message', 'files', 'both']);

test('a reassignment after initial authorization is checked again before writing', function (string $operation) {
    $before = ($this->caseSnapshot)();
    $realAccess = new ProjectAccess;
    $this->mock(ProjectAccess::class, function ($mock) use ($realAccess) {
        $calls = 0;
        $mock->shouldReceive('allows')->twice()->andReturnUsing(function ($staff, $clientId) use (&$calls, $realAccess) {
            $allowed = $realAccess->allows($staff, $clientId);
            if (++$calls === 1) {
                $this->assignmentA->update(['advisor_id' => $this->advisorB->id]);
            }

            return $allowed;
        });
    });
    $payload = ['_token' => 'reassignment-valid-csrf', 'submission_id' => (string) Str::uuid(),
        'admin_message' => 'NO GUARDAR TRAS REASIGNAR',
        'admin_files' => [UploadedFile::fake()->create('rechazado.pdf', 1, 'application/pdf')], 'progress_status' => 'delivered'];
    $this->actingAs($this->advisorA)->post(route($operation === 'return' ? 'admin.works.store' : 'admin.works.progress', $this->clientA), $payload)->assertForbidden();
    expect(($this->caseSnapshot)())->toBe($before)
        ->and($this->assignmentA->fresh()->advisor_id)->toBe($this->advisorB->id)
        ->and($this->assignmentA->fresh()->progress_status)->toBe('received');
    $this->assertDatabaseCount('user_activities', 0);
})->with(['return', 'progress']);

test('a revoked advisor receives a normal json rejection without private draft data', function () {
    $this->assignmentA->update(['advisor_id' => $this->advisorB->id]);
    $before = ($this->caseSnapshot)();
    $this->actingAs($this->advisorA)->postJson(route('admin.works.store', $this->clientA), [
        '_token' => 'reassignment-valid-csrf', 'submission_id' => (string) Str::uuid(), 'admin_message' => 'TEXTO PRIVADO',
    ])->assertForbidden()->assertJsonStructure(['message'])->assertDontSee('TEXTO PRIVADO')->assertDontSee('<!doctype html>');
    expect(($this->caseSnapshot)())->toBe($before);
});
