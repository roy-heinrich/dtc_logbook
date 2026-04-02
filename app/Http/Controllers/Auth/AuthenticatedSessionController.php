<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
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
    public function store(LoginRequest $request): Response
    {
        try {
            $email = (string) $request->input('email', '');
            $adminRecord = Admin::query()
                ->select(['id', 'email', 'is_active'])
                ->where('email', $email)
                ->first();

            Log::info('login.attempt.started', [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'cache_store' => config('cache.default'),
                'db_connection' => config('database.default'),
                'db_host' => config('database.connections.pgsql.host'),
                'db_port' => config('database.connections.pgsql.port'),
                'db_database' => config('database.connections.pgsql.database'),
                'db_server_version' => DB::selectOne('select version() as v')->v ?? null,
                'admin_email_exists' => (bool) $adminRecord,
                'admin_id' => $adminRecord?->id,
                'admin_active' => $adminRecord?->is_active,
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

            if ($exception instanceof ValidationException) {
                $message = collect($exception->errors())
                    ->flatten()
                    ->first() ?? $exception->getMessage();

                $email = (string) $request->input('email', '');
                $adminExists = Admin::query()->where('email', $email)->exists();
                $adminCount = Admin::query()->count();
                $dbHost = (string) config('database.connections.pgsql.host');
                $dbName = (string) config('database.connections.pgsql.database');
                if (! $adminExists) {
                    $message .= ' (No admin account with this email was found in the current database connection.)';
                }

                $showActualErrors = filter_var(env('SHOW_ACTUAL_ERRORS', false), FILTER_VALIDATE_BOOL);
                if ($showActualErrors) {
                    $message .= sprintf(' [db_host=%s db_name=%s admins=%d]', $dbHost, $dbName, $adminCount);
                }

                return response()->view('auth.login', [
                    'loginError' => $message,
                    'prefillEmail' => $email,
                ], 200);
            }

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
