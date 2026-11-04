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
use Livewire\LivewireManager;

class InactiveAccountCsrfMiddleware extends PreventRequestForgery
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
    $this->app->bind(PreventRequestForgery::class, InactiveAccountCsrfMiddleware::class);
    $this->withSession(['_token' => 'inactive-valid-csrf']);
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

test('an inactive account loses its open session and needs a fresh login after reactivation', function (string $role, string $operation) {
    $actor = match ($role) {
        'client' => $this->clientA, 'advisor' => $this->advisorA, 'admin' => $this->admin
    };
    $this->actingAs($actor)->get(route('dashboard'))->assertOk();
    $this->get(route('documents.file.show', $this->documentsA[0]))->assertOk();
    $before = ($this->caseSnapshot)();
    $assignment = $this->assignmentA->fresh()->getRawOriginal();
    $profile = $actor->fresh()->getRawOriginal();
    unset($profile['updated_at'],$profile['remember_token']);
    DB::table('users')->where('id', $actor->id)->update(['status' => 'inactive']);
    $response = match ($operation) {
        'dashboard' => $this->get(route('dashboard')),
        'document' => $this->get(route('documents.file.show', $this->documentsA[0])),
        'send' => $this->post(route($role === 'client' ? 'private.upload-document.store' : 'admin.works.store', $role === 'client' ? [] : $this->clientA), [
            '_token' => 'inactive-valid-csrf', 'submission_id' => (string) Str::uuid(), 'draft_owner' => $actor->id,
            'additional_information' => 'BORRADOR INACTIVO', 'admin_message' => 'BORRADOR INACTIVO',
            ($role === 'client' ? 'document_files' : 'admin_files') => [UploadedFile::fake()->create('rechazado.pdf', 1, 'application/pdf')],
        ]),
    };
    $response->assertForbidden()->assertSee('Tu cuenta está desactivada')->assertDontSee('BORRADOR INACTIVO')
        ->assertDontSee('EXPEDIENTE-PRIVADO-A')->assertHeader('Cache-Control', 'no-store, private')->assertSessionMissing('_old_input');
    $this->assertGuest();
    expect(($this->caseSnapshot)())->toBe($before)->and($this->assignmentA->fresh()->getRawOriginal())->toBe($assignment);
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('login'))->assertOk();
    $this->post(route('login.store'), ['_token' => session()->token(), 'email' => $actor->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
    $actor->refresh()->forceFill(['status' => 'active'])->save();
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('login'))->assertOk();
    $this->post(route('login.store'), ['_token' => session()->token(), 'email' => $actor->email, 'password' => 'password'])->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($actor);
    $this->get(route('dashboard'))->assertOk();
    $this->get(route('documents.file.show', $this->documentsA[0]))->assertOk();
    $page = $role === 'client' ? route('private.upload-document') : route('admin.works.show', $this->clientA);
    $this->get($page)->assertOk()->assertSee('MENSAJE-PRIVADO-A')->assertSee('INDICACION-PRIVADA-A');
    $afterProfile = $actor->fresh()->getRawOriginal();
    unset($afterProfile['updated_at'],$afterProfile['remember_token']);
    expect($afterProfile)->toBe($profile)->and(($this->caseSnapshot)())->toBe($before);
})->with(['client', 'advisor', 'admin'])->with(['dashboard', 'document', 'send']);

test('inactive accounts cannot bypass the guard through settings or livewire', function (string $endpoint) {
    $this->clientA->forceFill(['status' => 'inactive'])->save();
    $before = ($this->caseSnapshot)();
    $this->actingAs($this->clientA);
    if ($endpoint === 'settings') {
        $response = $this->get(route('profile.edit'));
    } else {
        $response = $this->withHeader('X-Livewire', 'true')->postJson(app(LivewireManager::class)->getUpdateUri(), ['_token' => 'inactive-valid-csrf', 'components' => []]);
    }
    $response->assertForbidden()->assertHeader('Cache-Control', 'no-store, private');
    if ($endpoint === 'livewire') {
        $response->assertJson(['message' => 'Tu cuenta está desactivada. Se cerró esta sesión.']);
    }
    $this->assertGuest();
    expect(($this->caseSnapshot)())->toBe($before);
})->with(['settings', 'livewire']);
