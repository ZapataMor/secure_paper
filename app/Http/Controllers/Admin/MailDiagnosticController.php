<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Dotenv\Dotenv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MailDiagnosticController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'admin' && $user->status === 'active'
            && $user->email_verified_at !== null && $user->deleted_at === null, 403);

        // Only fixed labels and booleans leave the server. Never expose values,
        // file contents, DSNs, passwords, parsing errors or exception traces.
        $fileMailer = 'missing';
        $fileReadable = is_readable(app()->environmentFilePath());
        $fileParsed = false;
        if ($fileReadable) {
            try {
                $variables = Dotenv::parse(file_get_contents(app()->environmentFilePath()));
                $fileMailer = $this->mailerLabel($variables['MAIL_MAILER'] ?? null);
                $fileParsed = true;
                unset($variables);
            } catch (Throwable $exception) {
                $fileMailer = 'unavailable';
                unset($exception);
            }
        }

        $password = (string) config('mail.mailers.smtp.password');
        $result = [
            'diagnostic_version' => 27,
            'smtp_connection_tested' => false,
            'mail_sent' => false,
            'configuration_cached' => app()->configurationIsCached(),
            'environment_file_standard' => app()->environmentFile() === '.env',
            'environment_file_readable' => $fileReadable,
            'environment_file_parsed' => $fileParsed,
            'mail_default' => $this->mailerLabel(config('mail.default')),
            'mail_mailer_environment' => $this->mailerLabel(env('MAIL_MAILER')),
            'mail_mailer_file' => $fileMailer,
            'smtp_transport_configured' => config('mail.mailers.smtp.transport') === 'smtp',
            'host_matches_gmail' => config('mail.mailers.smtp.host') === 'smtp.gmail.com',
            'port_matches_587' => (int) config('mail.mailers.smtp.port') === 587,
            'scheme_matches_smtp' => config('mail.mailers.smtp.scheme') === 'smtp',
            'url_override_present' => ! empty(config('mail.mailers.smtp.url')),
            'username_matches_sender' => config('mail.mailers.smtp.username') === 'comunicaciones@propaps.com',
            'from_matches_sender' => config('mail.from.address') === 'comunicaciones@propaps.com',
            'password_present' => $password !== '',
            'password_has_whitespace' => preg_match('/\s/', $password) === 1,
            'password_has_16_characters' => strlen($password) === 16,
        ];
        unset($password);

        return response()->json($result)->withHeaders([
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }

    private function mailerLabel(mixed $mailer): string
    {
        if ($mailer === null || $mailer === '') {
            return 'missing';
        }

        return in_array($mailer, ['smtp', 'log', 'array', 'failover', 'sendmail', 'roundrobin'], true)
            ? $mailer : 'other';
    }
}
