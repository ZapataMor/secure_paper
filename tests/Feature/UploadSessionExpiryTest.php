<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class SessionExpiryCsrfMiddleware extends PreventRequestForgery
{
    protected function runningUnitTests()
    {
        return false;
    }
}

beforeEach(function () {
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');
    config(['project_notifications.enabled' => false]);
    $this->app->bind(PreventRequestForgery::class, SessionExpiryCsrfMiddleware::class);
    Http::preventStrayRequests();
    Mail::fake();
    Notification::fake();
    Queue::fake();
    Storage::fake('local');
    $this->client = User::factory()->create(['role' => 'client', 'status' => 'active']);
    $this->client->appendAdditionalInformation('INDICACIONES DEL EXPEDIENTE');
    $this->client->save();
    $plan = PaymentPlan::create(['name' => 'PRUEBA SESION SIN COBRO', 'price' => 0, 'duration_days' => 30, 'status' => 'active']);
    $this->subscription = UserSubscription::create(['user_id' => $this->client->id, 'payment_plan_id' => $plan->id,
        'start_date' => now('America/Bogota')->subDay(), 'end_date' => now('America/Bogota')->addDays(29),
        'status' => 'active', 'payment_status' => 'paid', 'amount_paid' => 0]);
    $type = DocumentType::create(['name' => 'PDF']);
    Storage::disk('local')->put('documents/conservado.pdf', 'PDF FICTICIO CONSERVADO');
    Document::create(['user_id' => $this->client->id, 'uploaded_by' => $this->client->id,
        'document_type_id' => $type->id, 'title' => 'Conservado', 'original_name' => 'conservado.pdf',
        'file_path' => 'documents/conservado.pdf', 'mime_type' => 'application/pdf', 'file_size' => 24, 'uploaded_at' => now()]);
    $this->snapshot = function () {
        $snapshot = [];
        foreach (['users', 'documents', 'user_messages', 'project_assignments', 'user_subscriptions', 'payments', 'wompi_orders'] as $table) {
            $snapshot[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        foreach (Storage::disk('local')->allFiles() as $file) {
            $snapshot[$file] = hash('sha256', Storage::disk('local')->get($file));
        }

        return $snapshot;
    };
});
afterEach(function () {
    Http::assertNothingSent();
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
    Queue::assertNothingPushed();
});

test('an expired csrf token rejects the upload without reflecting or storing the draft', function () {
    $before = ($this->snapshot)();
    $this->actingAs($this->client)->withSession(['_token' => 'current-token'])
        ->post(route('private.upload-document.store'), ['_token' => 'expired-token', 'additional_information' => 'BORRADOR PRIVADO NO PUBLICADO'])
        ->assertStatus(419)->assertSee('Volver al expediente y recuperar')
        ->assertSee('Este envío no se guardó')->assertDontSee('BORRADOR PRIVADO NO PUBLICADO')
        ->assertHeader('Cache-Control', 'no-store, private')->assertSessionMissing('_old_input');
    expect(($this->snapshot)())->toBe($before);
});

test('a guest upload returns to the form after login without replaying the post', function () {
    $before = ($this->snapshot)();
    $this->withHeader('Sec-Fetch-Site', 'same-origin')->from('https://example.test/irrelevante')
        ->post(route('private.upload-document.store'), ['additional_information' => 'BORRADOR NO ENVIADO'])
        ->assertRedirect(route('login'))->assertSessionHas('url.intended', route('private.upload-document'));
    $this->post(route('login.store'), ['email' => $this->client->email, 'password' => 'password'])
        ->assertRedirect(route('private.upload-document'));
    $this->get(route('private.upload-document'))->assertOk()->assertSee('Recuperar indicaciones')
        ->assertDontSee('BORRADOR NO ENVIADO')->assertSee('INDICACIONES DEL EXPEDIENTE');
    expect(($this->snapshot)())->toBe($before);
});

test('a stale form from a different account cannot save its draft under the current account', function (bool $malformed) {
    $other = User::factory()->create(['role' => 'client', 'status' => 'active']);
    $subscription = $this->subscription->replicate();
    $subscription->user_id = $other->id;
    $subscription->save();
    $before = ($this->snapshot)();
    $this->actingAs($other)->withHeader('Sec-Fetch-Site', 'same-origin')
        ->post(route('private.upload-document.store'), ['draft_owner' => $malformed ? [$other->id] : $this->client->id, 'additional_information' => 'BORRADOR AJENO'])
        ->assertForbidden()->assertSessionMissing('_old_input');
    expect(($this->snapshot)())->toBe($before);
})->with([false, true]);

test('json requests keep their csrf error response and do not receive html recovery content', function () {
    $before = ($this->snapshot)();
    $this->postJson(route('private.upload-document.store'), ['_token' => 'expired-token', 'additional_information' => 'BORRADOR PRIVADO'])
        ->assertStatus(419)->assertJsonStructure(['message'])->assertDontSee('BORRADOR PRIVADO')
        ->assertDontSee('Volver al expediente y recuperar');
    expect(($this->snapshot)())->toBe($before);
});

test('a new valid token allows a deliberate send and confirms draft cleanup', function () {
    $this->actingAs($this->client)->withSession(['_token' => 'new-token'])
        ->post(route('private.upload-document.store'), ['_token' => 'new-token', 'draft_owner' => $this->client->id,
            'additional_information' => 'RESPUESTA RECUPERADA Y CONFIRMADA'])
        ->assertRedirect(route('private.upload-document'))->assertSessionHas('upload_saved', true)->assertSessionHasNoErrors();
    expect($this->client->fresh()->additionalInformationEntries())->toHaveCount(2);
    $this->assertDatabaseCount('documents', 1);
});
