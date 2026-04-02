<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            Log::info('login.attempt.started', [
                'email' => (string) $request->input('email', ''),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'cache_store' => config('cache.default'),
                'redis_client' => config('database.redis.client'),
                'has_redis_url' => !empty(env('REDIS_URL')),
            ]);

            $request->authenticate();

            $admin = Auth::guard('admin')->user();
            Log::info('login.attempt.authenticated', [
                'admin_id' => $admin?->id,
                'email' => $admin?->email,
            ]);

            $request->session()->regenerate();

            Log::info('login.attempt.session_regenerated', [
                'session_id' => $request->session()->getId(),
            ]);

            $target = route('admin.dashboard', absolute: false);
            Log::info('login.attempt.redirecting', [
                'target' => $target,
            ]);

            return redirect()->intended($target);
        } catch (Throwable $exception) {
            Log::error('login.attempt.failed', [
                'email' => (string) $request->input('email', ''),
                'ip' => $request->ip(),
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            $showActualErrors = filter_var(env('SHOW_ACTUAL_ERRORS', false), FILTER_VALIDATE_BOOL);
            $message = $showActualErrors
                ? 'Login failed: ' . $exception->getMessage()
                : 'Login failed. Please try again.';

            return response()->view('auth.login', [
                'loginError' => $message,
                'prefillEmail' => (string) $request->input('email', ''),
            ], 422);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
