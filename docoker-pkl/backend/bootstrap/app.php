<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->append(App\Http\Middleware\Cors::class);
    $middleware->api(prepend: [
        \App\Http\Middleware\ForceJsonResponse::class,
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        
    ]);
    
    $middleware->validateCsrfTokens(except: [
        'api/*',
        'login',
        'logout',
    ]);
    
    $middleware->redirectGuestsTo('/login');
})
    ->withExceptions(function (Exceptions $exceptions) {
        // Tangani unauthenticated untuk API
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated. Please login first.'
                ], 401);
            }
        });
    })->create();