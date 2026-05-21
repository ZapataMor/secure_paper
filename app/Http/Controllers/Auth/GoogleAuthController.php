<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret') || ! config('services.google.redirect')) {
            Log::error('Google OAuth is not configured.');

            return redirect()
                ->route('login')
                ->with('status', 'El inicio de sesion con Google no esta configurado correctamente.');
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (Throwable $exception) {
            Log::error('Google OAuth redirect failed.', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('status', 'No pudimos iniciar sesion con Google. Intentalo de nuevo mas tarde.');
        }
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::error('Google OAuth callback failed.', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('status', 'No pudimos completar el inicio de sesion con Google.');
        }

        $user = User::where('google_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => bcrypt(Str::random(24)),
                'role' => 'client',
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
