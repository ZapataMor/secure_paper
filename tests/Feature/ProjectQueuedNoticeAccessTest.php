<?php

use App\Jobs\SendProjectUpdate;
use App\Mail\ProjectUpdateMail;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Services\ProjectAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');
    Http::preventStrayRequests();
    Mail::fake();
    Notification::fake();
    // El nombre activa la lógica del trabajo; el transporte de respaldo solo guarda en memoria.
    config(['project_notifications.enabled' => true, 'mail.default' => 'smtp',
        'mail.mailers.smtp' => ['transport' => 'array']]);
    $this->client = User::factory()->create(['role' => 'client', 'status' => 'active', 'email' => 'cliente-avisos@propaps.test']);
    $this->advisor = User::factory()->create(['role' => 'advisor', 'status' => 'active', 'email' => 'asesor-avisos@propaps.test']);
    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'email' => 'admin-avisos@propaps.test']);
    config(['project_notifications.admin_recipients' => [$this->admin->email]]);
    $this->assignment = ProjectAssignment::create([
        'client_id' => $this->client->id, 'advisor_id' => $this->advisor->id,
        'assigned_by' => $this->admin->id, 'project_title' => 'EXPEDIENTE CONFIDENCIAL FICTICIO',
        'progress_status' => 'received',
    ]);
});

afterEach(function () {
    Notification::assertNothingSent();
    expect(DB::table('payments')->count())->toBe(0)
        ->and(DB::table('wompi_orders')->count())->toBe(0)
        ->and(DB::table('jobs')->count())->toBe(0);
});

test('queued notices reach only the current authorized recipient without case contents', function (string $audience, string $event) {
    $recipient = $this->{$audience};
    $job = new SendProjectUpdate($this->client->id, $recipient->id, $audience, $event);
    expect($job->connection)->toBe('database')->and($job->queue)->toBe('project-mail');
    $before = $this->assignment->fresh()->getAttributes();
    $job->handle(app(ProjectAccess::class));
    Mail::assertSent(ProjectUpdateMail::class, function ($mail) use ($recipient, $audience) {
        expect($mail->workspaceUrl)->toBe($audience === 'client'
            ? route('private.upload-document') : route('admin.works.show', $this->client));
        $html = $mail->render();
        expect($html)->toContain('Hay novedades')
            ->not->toContain('EXPEDIENTE CONFIDENCIAL FICTICIO');
        expect($mail->attachments)->toBe([]);

        return $mail->hasTo($recipient->email);
    });
    Mail::assertSentCount(1);
    expect($this->assignment->fresh()->getAttributes())->toBe($before);
})->with([['client', 'return'], ['advisor', 'upload'], ['admin', 'upload']]);

test('a queued notice is discarded when recipient access changes before execution', function (string $change) {
    $audience = $change === 'admin_removed' ? 'admin' : 'advisor';
    $recipient = $this->{$audience};
    $job = new SendProjectUpdate($this->client->id, $recipient->id, $audience, 'upload');
    match ($change) {
        'reassigned' => $this->assignment->update(['advisor_id' => User::factory()->create(['role' => 'advisor', 'status' => 'active'])->id]),
        'inactive_recipient' => $recipient->forceFill(['status' => 'inactive'])->save(),
        'deleted_recipient' => $recipient->delete(),
        'unverified_recipient' => $recipient->update(['email_verified_at' => null]),
        'inactive_client' => $this->client->forceFill(['status' => 'inactive'])->save(),
        'admin_removed' => config(['project_notifications.admin_recipients' => []]),
    };
    $before = $this->assignment->fresh()->getAttributes();
    $job->handle(app(ProjectAccess::class));
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    expect($this->assignment->fresh()->getAttributes())->toBe($before);
})->with(['reassigned', 'inactive_recipient', 'deleted_recipient', 'unverified_recipient', 'inactive_client', 'admin_removed']);

test('project notices respect disabled mail transport and reject a different client', function (string $scenario) {
    $recipient = $this->client;
    if ($scenario === 'disabled') {
        config(['project_notifications.enabled' => false]);
    }
    if ($scenario === 'log') {
        config(['mail.default' => 'log']);
    }
    if ($scenario === 'other_client') {
        $recipient = User::factory()->create(['role' => 'client', 'status' => 'active']);
    }
    (new SendProjectUpdate($this->client->id, $recipient->id, 'client', 'return'))->handle(app(ProjectAccess::class));
    Mail::assertNothingSent();
    Mail::assertNothingQueued();
})->with(['disabled', 'log', 'other_client']);
