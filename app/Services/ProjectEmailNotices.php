<?php

namespace App\Services;

use App\Jobs\SendProjectUpdate;
use App\Models\ProjectAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectEmailNotices
{
    public function schedule(int $clientId, string $event): void
    {
        if (!config('project_notifications.enabled') || config('mail.default') !== 'smtp' || !isset(SendProjectUpdate::NOTICES[$event])) return;
        DB::afterCommit(function () use ($clientId, $event): void {
            try {
                $recipients = [];
                if (in_array($event, ['return', 'progress', 'assignment'], true)) $recipients[$clientId] = 'client';
                if (in_array($event, ['upload', 'assignment'], true)) {
                    $advisorId = ProjectAssignment::where('client_id', $clientId)->value('advisor_id');
                    if ($advisorId) $recipients[(int) $advisorId] = 'advisor';
                }
                if ($event === 'upload') {
                    foreach (User::where('role', 'admin')->where('status', 'active')
                        ->whereNotNull('email_verified_at')
                        ->whereIn('email', config('project_notifications.admin_recipients', []))
                        ->pluck('id') as $id) $recipients[(int) $id] = 'admin';
                }
                foreach ($recipients as $id => $audience) SendProjectUpdate::dispatch($clientId, $id, $audience, $event);
            } catch (\Throwable $exception) {
                // Un problema de correo no debe borrar documentos que ya se guardaron correctamente.
                Log::warning('Project email could not be queued.', ['exception' => $exception::class]);
            }
        });
    }
}
