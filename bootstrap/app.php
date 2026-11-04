<?php

use App\Http\Middleware\EnsureConfiguredEmailVerification;
use App\Http\Middleware\EnsureUserHasActiveMembership;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestForgery(except: ['pagos/wompi/eventos/*']);
        $middleware->alias([
            'verified' => EnsureConfiguredEmailVerification::class,
            'admin' => EnsureUserIsAdmin::class,
            'role' => RoleMiddleware::class,
            'membership' => EnsureUserHasActiveMembership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, Throwable $exception, Request $request) {
            if (! $request->isMethod('POST') || ! $request->is('dashboard/cargar-documento') || $request->expectsJson()) {
                return $response;
            }
            if ($exception instanceof AuthenticationException) {
                $request->session()->put('url.intended', route('private.upload-document'));

                return redirect()->route('login')->with('status',
                    'Vuelve a ingresar con la misma cuenta. En tu expediente podrás recuperar las indicaciones si hay un borrador temporal disponible.');
            }
            if ($response->getStatusCode() === 419) {
                return response()->view('errors.upload-session-expired', [], 419)
                    ->header('Cache-Control', 'no-store, private');
            }

            return $response;
        });
    })->create();
