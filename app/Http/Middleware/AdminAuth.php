<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('admin');

        if (!$guard->check()) {
            return redirect()->route('login');
        }

        $admin = $guard->user();

        // Check if admin account is active
        if (!$admin->is_active) {
            $guard->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        if (
            $admin->must_change_password &&
            ! $request->routeIs('profile.edit') &&
            ! $request->routeIs('password.update') &&
            ! $request->routeIs('logout')
        ) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Please change your password before continuing.');
        }

        return $next($request);
    }
}
