<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasActiveMembership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isAdmin() || $user?->hasActiveMembership()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Necesitas una membresia activa y pagada para acceder al modulo Cargar documento.');
        }

        return redirect()
            ->route('private.planes')
            ->with('membership_error', 'Necesitas una membresia activa y pagada para acceder al modulo "Cargar documento".');
    }
}
