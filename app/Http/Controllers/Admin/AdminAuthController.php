<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRefreshToken;
use App\Models\LoginLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $token = Auth::guard('admin_api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $admin = Auth::guard('admin_api')->user();
        if (! $admin->is_active) {
            Auth::guard('admin_api')->logout();
            return response()->json(['message' => 'Account is inactive.'], 403);
        }

        // Log the login
        LoginLog::create([
            'user_id' => $admin->id,
            'user_type' => Admin::class,
            'login_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $refreshToken = $this->issueRefreshToken($admin);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('admin_api')->factory()->getTTL() * 60,
            'refresh_token' => $refreshToken,
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $tokenHash = hash('sha256', $data['refresh_token']);
        $storedToken = AdminRefreshToken::where('token_hash', $tokenHash)->first();

        if (! $storedToken || $storedToken->revoked_at || $storedToken->expires_at->isPast()) {
            return response()->json(['message' => 'Refresh token is invalid.'], 401);
        }

        $admin = Admin::find($storedToken->admin_id);
        if (! $admin || ! $admin->is_active) {
            return response()->json(['message' => 'Account is inactive.'], 403);
        }

        $newRefreshToken = $this->rotateRefreshToken($storedToken, $admin);
        $accessToken = Auth::guard('admin_api')->login($admin);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('admin_api')->factory()->getTTL() * 60,
            'refresh_token' => $newRefreshToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $tokenHash = hash('sha256', $data['refresh_token']);
        $storedToken = AdminRefreshToken::where('token_hash', $tokenHash)->first();

        if ($storedToken && ! $storedToken->revoked_at) {
            $storedToken->update(['revoked_at' => now()]);
        }

        Auth::guard('admin_api')->logout();

        return response()->json(['message' => 'Logged out.']);
    }

    private function issueRefreshToken(Admin $admin): string
    {
        $rawToken = Str::random(64);

        AdminRefreshToken::create([
            'admin_id' => $admin->id,
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => now()->addDays(config('auth.admin_refresh_ttl_days')),
        ]);

        return $rawToken;
    }

    private function rotateRefreshToken(AdminRefreshToken $storedToken, Admin $admin): string
    {
        $newRawToken = Str::random(64);

        $newToken = AdminRefreshToken::create([
            'admin_id' => $admin->id,
            'token_hash' => hash('sha256', $newRawToken),
            'expires_at' => now()->addDays(config('auth.admin_refresh_ttl_days')),
        ]);

        $storedToken->update([
            'revoked_at' => now(),
            'replaced_by_id' => $newToken->id,
        ]);

        return $newRawToken;
    }
}
