<?php

namespace App\Jobs;

use App\Mail\ProjectUpdateMail;
use App\Models\User;
use App\Services\ProjectAccess;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendProjectUpdate implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 30;

    public const NOTICES = [
        'upload' => 'Hay nuevos documentos o indicaciones para revisar.',
        'return' => 'Tu equipo compartió una actualización o nuevos archivos contigo.',
        'assignment' => 'Se actualizó la asignación de un proyecto en tu espacio de trabajo.',
        'progress' => 'Se actualizó el estado de tu proyecto.',
    ];

    public function __construct(
        public readonly int $clientId,
        public readonly int $recipientId,
        public readonly string $audience,
        public readonly string $event,
    ) {
        $this->onConnection('database')->onQueue('project-mail');
    }

    public function backoff(): array { return [60, 300, 900]; }

    public function handle(ProjectAccess $access): void
    {
        if (!config('project_notifications.enabled') || config('mail.default') !== 'smtp' || !isset(self::NOTICES[$this->event])) return;
        $client = User::find($this->clientId);
        $recipient = User::find($this->recipientId);
        if (!$client || !$client->isClient() || $client->status !== 'active' || !$recipient || $recipient->status !== 'active'
            || !$recipient->hasVerifiedEmail() || !filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) return;

        // Volver a comprobar el acceso al enviar: una reasignación puede revocarlo mientras espera en cola.
        if ($this->audience === 'client') {
            if ($recipient->id !== $client->id || !$recipient->isClient()) return;
            $url = route('private.upload-document');
        } elseif ($this->audience === 'advisor') {
            if (!$recipient->isAdvisor() || !$access->allows($recipient, $client->id)) return;
            $url = route('admin.works.show', $client);
        } elseif ($this->audience === 'admin') {
            if (!$recipient->isAdmin()) return;
            if (!in_array(strtolower($recipient->email), config('project_notifications.admin_recipients', []), true)) return;
            $url = route('admin.works.show', $client);
        } else {
            return;
        }
        Mail::to($recipient->email)->send(new ProjectUpdateMail(self::NOTICES[$this->event], $url));
    }
}
