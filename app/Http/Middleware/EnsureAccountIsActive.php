<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $current = User::select('id', 'status', 'session_epoch')->find($user->id);
        $active = $current && $current->status === 'active';
        $issued = $request->session()->get('account_session');
        // Las sesiones previas a esta mejora siguen válidas solo si nunca se revocaron.
        $legacy = $issued === null && $current?->session_epoch === null;
        $matches = is_array($issued) && ($issued['user_id'] ?? null) === $user->id
            && array_key_exists('epoch', $issued) && $issued['epoch'] === $current?->session_epoch;
        if ($active && ($legacy || $matches)) {
            return $next($request);
        }

        if ($active) {
            // No invalida el recordatorio de una sesión nueva legítima.
            Auth::guard('web')->logoutCurrentDevice();
        } else {
            Auth::guard('web')->logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = $active ? 'Esta sesión fue cerrada. Vuelve a ingresar para continuar.'
            : 'Tu cuenta está desactivada. Se cerró esta sesión.';
        $response = $request->expectsJson()
            ? response()->json(['message' => $message], 403)
            : response()->view($active ? 'errors.session-revoked' : 'errors.account-inactive', [], 403);

        return $response->header('Cache-Control', 'no-store, private');
    }
}
