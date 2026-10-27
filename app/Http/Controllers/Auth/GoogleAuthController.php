<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LegalConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    private const PENDING = 'google.registration';

    public function redirect(Request $request)
    {
        $request->session()->forget(self::PENDING);
        if (!config('services.google.client_id') || !config('services.google.client_secret') || !config('services.google.redirect')) {
            return $this->failed();
        }
        // Start OAuth on the configured callback origin so its session state survives www redirects.
        $callback = parse_url((string) config('services.google.redirect'));
        if (!$callback || empty($callback['host']) || empty($callback['scheme'])) { return $this->failed(); }
        $port = isset($callback['port']) ? ':'.$callback['port'] : '';
        $origin = $callback['scheme'].'://'.$callback['host'].$port;
        if ($request->getSchemeAndHttpHost() !== $origin) {
            return redirect()->to($origin.'/auth/google');
        }
        try { return Socialite::driver('google')->redirect(); }
        catch (Throwable $exception) { return $this->failed($exception); }
    }

    public function callback(Request $request)
    {
        $request->session()->forget(self::PENDING);
        try {
            // Socialite validates the one-time OAuth state held in the session.
            $identity = Socialite::driver('google')->user();
            $raw = $identity->getRaw();
            $verified = $raw['email_verified'] ?? $raw['verified_email'] ?? false;
            $id = (string) $identity->getId();
            $email = Str::lower(trim((string) $identity->getEmail()));
            if (!in_array($verified, [true, 'true'], true) || $id === '' || strlen($id) > 255 || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->failed();
            }
            $avatar = $identity->getAvatar();
            // La foto es opcional. No truncar su URL: puede invalidarla y bloquear el alta por VARCHAR(255).
            $avatar = is_string($avatar) && strlen($avatar) <= 255 && filter_var($avatar, FILTER_VALIDATE_URL) ? $avatar : null;
            $profile = ['id' => $id, 'email' => $email, 'name' => Str::limit((string) $identity->getName(), 255, '') ?: 'Cliente',
                'avatar' => $avatar];
            $user = DB::transaction(function () use ($profile) {
                $byId = User::withTrashed()->where('google_id', $profile['id'])->lockForUpdate()->first();
                $byEmail = User::withTrashed()->where('email', $profile['email'])->lockForUpdate()->first();
                if ($byId && $byEmail && $byId->id !== $byEmail->id) { throw new \RuntimeException('Identity conflict'); }
                $user = $byId ?? $byEmail;
                if (!$user) { return null; }
                if ($user->trashed() || $user->status !== 'active' || ($user->google_id && $user->google_id !== $profile['id'])) {
                    throw new \RuntimeException('Account unavailable');
                }
                $fields = ['google_id' => $profile['id'], 'avatar' => $profile['avatar']];
                if ($user->email === $profile['email']) { $fields['email_verified_at'] = $user->email_verified_at ?? now(); }
                $user->forceFill($fields)->save();
                return $user;
            });
            if ($user) { return $this->signIn($request, $user); }
            $request->session()->regenerate();
            $request->session()->put(self::PENDING, [...$profile, 'expires_at' => now()->addMinutes(15)->timestamp]);
            return redirect()->route('google.confirm');
        } catch (Throwable $exception) { return $this->failed($exception, 'callback'); }
    }

    public function confirm(Request $request)
    {
        $profile = $this->pending($request);
        return $profile ? view('pages::auth.google-confirm', ['profile' => $profile]) : $this->failed(null, 'confirmation_context_missing');
    }

    public function register(Request $request)
    {
        $profile = $this->pending($request);
        if (!$profile) { return $this->failed(null, 'registration_context_missing'); }
        $request->validate(['terms' => ['required', 'accepted']], [
            'terms.required' => 'Debes aceptar los términos y la política de privacidad.',
            'terms.accepted' => 'Debes aceptar los términos y la política de privacidad.',
        ]);
        try {
            $user = DB::transaction(function () use ($profile) {
                if (User::withTrashed()->where('email', $profile['email'])->orWhere('google_id', $profile['id'])->exists()) {
                    throw new \RuntimeException('Account changed; authenticate again');
                }
                $user = User::create(['name' => $profile['name'], 'email' => $profile['email'], 'google_id' => $profile['id'],
                    'avatar' => $profile['avatar'], 'password' => Str::random(64), 'role' => 'client', 'email_verified_at' => now()]);
                app(LegalConsent::class)->record($user, 'google');
                return $user;
            });
            $request->session()->forget(self::PENDING);
            return $this->signIn($request, $user);
        } catch (Throwable $exception) {
            $request->session()->forget(self::PENDING);
            return $this->failed($exception, 'registration');
        }
    }

    private function pending(Request $request): ?array
    {
        $profile = $request->session()->get(self::PENDING);
        if (!is_array($profile) || ($profile['expires_at'] ?? 0) <= now()->timestamp) {
            $request->session()->forget(self::PENDING);
            return null;
        }
        return $profile;
    }

    private function signIn(Request $request, User $user)
    {
        $this->sanitizeIntendedDestination($request);
        $request->session()->regenerate();
        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            $request->session()->put(['login.id' => $user->id, 'login.remember' => false]);
            return redirect()->route('two-factor.login');
        }
        Auth::login($user);
        return redirect()->intended(route('dashboard'));
    }

    private function sanitizeIntendedDestination(Request $request): void
    {
        $destination = $request->session()->pull('url.intended');
        if (!is_string($destination) || $destination === '') { return; }
        // The destination belongs to this application; never carry an external redirect through login.
        $decoded = rawurldecode($destination);
        if (preg_match('/[\\x00-\\x20\\x7f]/', $decoded) || str_contains($decoded, chr(92))) { return; }
        $parts = parse_url($destination);
        $origin = parse_url((string) config('app.url'));
        if ($parts === false || $origin === false || isset($parts['user']) || isset($parts['pass'])) { return; }
        if (isset($parts['host']) || isset($parts['scheme'])) {
            if (!isset($parts['host'], $parts['scheme'], $origin['host'], $origin['scheme'])
                || strtolower($parts['host']) !== strtolower($origin['host'])
                || strtolower($parts['scheme']) !== strtolower($origin['scheme'])
                || ($parts['port'] ?? ($parts['scheme'] === 'https' ? 443 : 80)) !== ($origin['port'] ?? ($origin['scheme'] === 'https' ? 443 : 80))) { return; }
        }
        $path = $parts['path'] ?? '/';
        $decodedPath = rawurldecode($path);
        if (!str_starts_with($decodedPath, '/') || str_starts_with($decodedPath, '//')) { return; }
        // A relative destination stays on the authenticated origin. Route authorization still applies.
        $request->session()->put('url.intended', $path.(isset($parts['query']) ? '?'.$parts['query'] : ''));
    }

    private function failed(?Throwable $exception = null, string $stage = 'authentication')
    {
        // Un único diagnóstico acotado; nunca guardar el mensaje de excepción, SQL, identidad o tokens.
        try {
            $directory = storage_path('app/private');
            \Illuminate\Support\Facades\File::ensureDirectoryExists($directory, 0700);
            \Illuminate\Support\Facades\File::put($directory.'/google-auth-diagnostic.json', json_encode([
                'occurred_at' => now()->utc()->toIso8601String(),
                'stage' => $stage,
                'exception_class' => $exception ? $exception::class : null,
                'sql_state' => $exception instanceof \Illuminate\Database\QueryException ? (string) ($exception->errorInfo[0] ?? '') : null,
                'database_error_code' => $exception instanceof \Illuminate\Database\QueryException ? (int) ($exception->errorInfo[1] ?? 0) : null,
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), true);
        } catch (Throwable) {
            // La ausencia de diagnóstico nunca debe impedir la respuesta de autenticación.
        }
        if ($exception) { Log::warning('Google sign-in could not complete.', ['exception' => $exception::class]); }
        return redirect()->route('login')->with('status', 'No pudimos completar el acceso con Google. Vuelve a intentarlo o utiliza tu correo y contraseña.');
    }
}
