<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminAuth::class,
            'super_admin' => \App\Http\Middleware\SuperAdminAuth::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle all exceptions - log detailed info, return generic message
        $exceptions->render(function (Throwable $e) {
            // Log detailed error server-side with full context
            logger()->error('Application Exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]);

            // Return generic message to user
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'An error occurred. Please try again later.',
                    'error' => 'internal_server_error',
                ], 500);
            }

            return response()->view('errors.500', [], 500);
        });

        // Handle HTTP exceptions (404, 403, etc.)
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->expectsJson();
        });
    })->create();
