<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

class EnsureConfiguredEmailVerification extends EnsureEmailIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        return config('accounts.email_verification_required')
            ? parent::handle($request, $next, $redirectToRoute)
            : $next($request);
    }
}
