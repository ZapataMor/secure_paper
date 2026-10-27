<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendProjectMailTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProjectMailTestController extends Controller
{
    private function authorizeRecipient(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && $user->isAdmin() && $user->status === 'active'
            && $user->hasVerifiedEmail() && $user->deleted_at === null
            && in_array(strtolower($user->email), config('project_notifications.admin_recipients', []), true), 403);
    }

    public function index(Request $request)
    {
        $this->authorizeRecipient($request);
        return response()->view('admin.mail-test', [
            'ready' => config('project_notifications.enabled') && config('mail.default') === 'smtp',
            'recipientEmail' => $request->user()->email,
        ])->header('Cache-Control', 'private, no-store')->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function store(Request $request)
    {
        $this->authorizeRecipient($request);
        if (!config('project_notifications.enabled') || config('mail.default') !== 'smtp') {
            return redirect()->route('admin.mail-test')->withErrors(['mail_test' => 'Activa los avisos y el correo antes de solicitar la prueba.']);
        }
        $key = 'project-mail-test:'.$request->user()->id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return redirect()->route('admin.mail-test')->withErrors(['mail_test' => 'La prueba ya fue solicitada. Revisa tu correo; podrás repetirla dentro de diez minutos.']);
        }
        RateLimiter::hit($key, 600);
        try {
            // El destinatario siempre es la cuenta autenticada; no acepta correos ni proyectos del formulario.
            SendProjectMailTest::dispatch((int) $request->user()->id);
        } catch (Throwable $exception) {
            RateLimiter::clear($key);
            Log::warning('Project mail test could not be queued.', ['exception_class' => get_class($exception)]);
            return redirect()->route('admin.mail-test')->withErrors(['mail_test' => 'No pudimos preparar la prueba. Inténtalo de nuevo más tarde.']);
        }
        return redirect()->route('admin.mail-test')->with('mail_test_queued', true);
    }
}
