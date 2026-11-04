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
        if (! $user || User::whereKey($user->id)->where('status', 'active')->exists()) {
            return $next($request);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = $request->expectsJson()
            ? response()->json(['message' => 'Tu cuenta está desactivada. Se cerró esta sesión.'], 403)
            : response()->view('errors.account-inactive', [], 403);

        return $response->header('Cache-Control', 'no-store, private');
    }
}
