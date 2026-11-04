<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PaymentPlan;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserSubscription;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class TwoAdminCsrfMiddleware extends PreventRequestForgery
{
    protected function runningUnitTests()
    {
        return false;
    }
}

class ChangeAdminAccessBeforeWrite
{
    public function handle($request, $next)
    {
        (app('test.change-admin-access'))();

        return $next($request);
    }
}

beforeEach(function () {
    expect(config('database.default'))->toBe('sqlite')
        ->and(config('database.connections.sqlite.database'))->toBe(':memory:');
    config(['project_notifications.enabled' => false]);
    $this->app->bind(PreventRequestForgery::class, TwoAdminCsrfMiddleware::class);
    $this->withSession(['_token' => 'two-admin-valid-csrf']);
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
    $this->secondAdmin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
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

test('an administrator cannot use an open confirmation after another administrator deactivates them', function (string $targetKind) {
    $target = $targetKind === 'admin' ? $this->admin : $this->clientA;
    $this->actingAs($this->secondAdmin)->get(route('admin.users.status.edit', ['user' => $target, 'status' => 'inactive']))->assertOk();
    $stale = ['_token' => 'two-admin-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => $target->session_epoch, 'confirmed' => 1, 'reason' => 'BORRADOR DEL ADMIN RETIRADO'];
    $this->actingAs($this->admin)->get(route('admin.users.status.edit', ['user' => $this->secondAdmin, 'status' => 'inactive']))->assertOk();
    $this->post(route('admin.users.status.update', $this->secondAdmin), ['_token' => 'two-admin-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => null, 'confirmed' => 1, 'reason' => 'RETIRO FICTICIO CONFIRMADO'])->assertSessionHasNoErrors();
    $case = ($this->caseSnapshot)();
    $users = DB::table('users')->orderBy('id')->get()->map(function ($u) {
        $u = (array) $u;
        unset($u['remember_token']);

        return $u;
    })->all();
    $audit = DB::table('user_activities')->orderBy('id')->get()->toJson();
    $this->actingAs($this->secondAdmin)->post(route('admin.users.status.update', $target), $stale)->assertForbidden()->assertDontSee($stale['reason']);
    $this->assertGuest();
    expect(($this->caseSnapshot)())->toBe($case)
        ->and(DB::table('users')->orderBy('id')->get()->map(function ($u) {
            $u = (array) $u;
            unset($u['remember_token']);

            return $u;
        })->all())->toBe($users)
        ->and(DB::table('user_activities')->orderBy('id')->get()->toJson())->toBe($audit)
        ->and($this->admin->fresh()->status)->toBe('active');
    $this->assertDatabaseCount('user_activities', 1);
})->with(['admin', 'client']);

test('access removed after route authorization is checked under the administrative lock', function (string $mode) {
    $actor = $this->secondAdmin;
    $case = ($this->caseSnapshot)();
    $targetBefore = $this->clientA->fresh()->getRawOriginal();
    $this->app->instance('test.change-admin-access', function () use ($actor, $mode) {
        $fresh = User::findOrFail($actor->id);
        if ($mode === 'role') {
            $fresh->forceFill(['role' => 'client'])->save();
        } else {
            $fresh->forceFill(['status' => 'inactive'])->save();
            if ($mode === 'cycle') {
                $fresh->forceFill(['status' => 'active'])->save();
            }
        }
    });
    $this->app['router']->aliasMiddleware('test.change-admin', ChangeAdminAccessBeforeWrite::class);
    $this->app['router']->getRoutes()->getByName('admin.users.status.update')->middleware('test.change-admin');
    $this->actingAs($actor)->post(route('admin.users.status.update', $this->clientA), [
        '_token' => 'two-admin-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => null, 'confirmed' => 1, 'reason' => 'NO DEBE GUARDARSE',
    ])->assertForbidden();
    expect(($this->caseSnapshot)())->toBe($case)->and($this->clientA->fresh()->getRawOriginal())->toBe($targetBefore)
        ->and($this->admin->fresh()->status)->toBe('active');
    $this->assertDatabaseCount('user_activities', 0);
})->with(['inactive', 'role', 'cycle']);

test('reactivation does not authorize an old admin session but a new login restores legitimate access', function () {
    $actor = $this->secondAdmin;
    $this->actingAs($actor)->withSession(['account_session' => ['user_id' => $actor->id, 'epoch' => null]])
        ->get(route('admin.users.status.edit', ['user' => $this->clientA, 'status' => 'inactive']))->assertOk();
    $fresh = User::findOrFail($actor->id);
    $fresh->forceFill(['status' => 'inactive'])->save();
    $fresh->forceFill(['status' => 'active'])->save();
    $case = ($this->caseSnapshot)();
    $this->post(route('admin.users.status.update', $this->clientA), ['_token' => 'two-admin-valid-csrf', 'status' => 'inactive', 'expected_status' => 'active', 'expected_epoch' => null, 'confirmed' => 1, 'reason' => 'FORMULARIO ANTIGUO'])
        ->assertForbidden()->assertDontSee('FORMULARIO ANTIGUO');
    $this->assertGuest();
    $this->assertDatabaseCount('user_activities', 0);
    $this->get(route('login'))->assertOk();
    $this->post(route('login.store'), ['_token' => session()->token(), 'email' => $actor->email, 'password' => 'password'])->assertSessionHasNoErrors();
    $this->get(route('admin.users.index'))->assertOk();
    $this->get(route('admin.users.status.edit', ['user' => $this->clientA, 'status' => 'inactive']))->assertOk();
    expect(($this->caseSnapshot)())->toBe($case)->and($this->clientA->fresh()->status)->toBe('active');
});
