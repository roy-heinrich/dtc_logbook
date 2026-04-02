<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_PREFIX
        );

        $middleware->web(append: [
            \App\Http\Middleware\SetResponseCacheHeaders::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminAuth::class,
            'super_admin' => \App\Http\Middleware\SuperAdminAuth::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle all exceptions - log detailed info, return generic message
        $exceptions->render(function (Throwable $e) {
            $showActualErrors = filter_var(env('SHOW_ACTUAL_ERRORS', false), FILTER_VALIDATE_BOOL);

            if (
                $e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Illuminate\Auth\AuthenticationException ||
                $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                $e instanceof \Illuminate\Session\TokenMismatchException
            ) {
                return null;
            }

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
                    'message' => $showActualErrors ? $e->getMessage() : 'An error occurred. Please try again later.',
                    'error' => 'internal_server_error',
                ], 500);
            }

            if ($showActualErrors) {
                return response()->make(
                    '<h1>Application Error</h1><pre style="white-space:pre-wrap">' . e($e->getMessage()) . '</pre>',
                    500,
                    ['Content-Type' => 'text/html; charset=UTF-8']
                );
            }

            return response()->view('errors.500', [], 500);
        });

        // Handle HTTP exceptions (404, 403, etc.)
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->expectsJson();
        });
    })->create();
