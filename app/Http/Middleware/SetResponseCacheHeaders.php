<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetResponseCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethodCacheable()) {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $isAuthRoute =
            $request->routeIs('login') ||
            $request->routeIs('logout') ||
            $request->routeIs('password.*') ||
            $request->routeIs('verification.*') ||
            $request->is('login') ||
            $request->is('logout') ||
            $request->is('forgot-password') ||
            $request->is('reset-password') ||
            $request->is('reset-password/*') ||
            $request->is('verify-email') ||
            $request->is('verify-email/*');

        if ($isAuthRoute) {
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');

            return $response;
        }

        if ($request->routeIs('admin.*') || $request->is('admin/*') || $request->user('admin')) {
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');

            return $response;
        }

        if ($response->isSuccessful()) {
            $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=600, stale-while-revalidate=60');
            $response->headers->set('Vary', 'Accept-Encoding', false);
        }

        return $response;
    }
}
