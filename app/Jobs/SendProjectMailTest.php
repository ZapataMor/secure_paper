<?php
namespace App\Jobs;

use App\Mail\ProjectMailTestMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendProjectMailTest implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;
    public int $tries = 1;
    public int $timeout = 30;

    public function __construct(public readonly int $recipientId)
    {
        $this->onConnection('database')->onQueue('project-mail');
    }

    public function handle(): void
    {
        if (!config('project_notifications.enabled') || config('mail.default') !== 'smtp') return;
        $user = User::find($this->recipientId);
        if (!$user || !$user->isAdmin() || $user->status !== 'active' || !$user->hasVerifiedEmail()
            || !in_array(strtolower($user->email), config('project_notifications.admin_recipients', []), true)) return;
        Mail::to($user->email)->send(new ProjectMailTestMail(
            'Este es un correo de prueba solicitado desde tu panel de administración. Confirma que los avisos pueden llegar a tu cuenta. No se modificó ningún proyecto, documento ni pago.',
            route('dashboard')
        ));
    }
}
