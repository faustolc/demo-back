<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // API-only middleware configuration
        $middleware->throttleApi();
        // This is the key part for Laravel 11/12 to prevent 'Route [login] not defined'
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Return null to prevent any redirection for API requests.
                // This allows the AuthenticationException to be thrown,
                // which our custom exception handler in withExceptions() will catch.
                return null;
            }

            // For web routes, you might still want to redirect to a login route
            // if you have one, e.g., return route('login');
            // But for an API-only app, you likely won't have this.
            return route('login'); // Fallback for non-API requests if 'login' route exists
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication failures
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'error' => 'Authentication required',
                ], 401);
            }
        });

        // Handle unauthorized access (403)
        $exceptions->render(function (UnauthorizedHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'error' => 'Access denied',
                ], 401);
            }
        });
    })->create();
