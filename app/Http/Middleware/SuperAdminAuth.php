<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = auth('admin');

        if (!$guard->check()) {
            return redirect()->route('admin.login');
        }

        $admin = $guard->user();

        if (!$admin->isSuperAdmin()) {
            abort(403, 'This action requires super admin privileges.');
        }

        return $next($request);
    }
}
