<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\LoginLog;
use App\Models\Role;
use App\Support\CacheVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of admins.
     */
    public function index()
    {
        $admins = Admin::select(['id', 'name', 'email', 'role_id', 'is_active', 'created_at', 'updated_at'])
            ->with(['role:id,name,display_name'])
            ->latest()
            ->paginate(15, ['*'], 'admins_page')
            ->withQueryString();
        $superAdminCount = Admin::whereHas('role', function ($query) {
            $query->where('name', 'super_admin');
        })->count();

        // Recent admin login logs
        $recentLogins = LoginLog::with('user')
            ->where('user_type', 'App\\Models\\Admin')
            ->orderByDesc('login_at')
            ->paginate(10, ['*'], 'recent_logins_page')
            ->withQueryString();

        return view('admin.admins.index', compact('admins', 'superAdminCount', 'recentLogins'));
    }

    /**
     * Display a listing of soft-deleted admins.
     */
    public function trash()
    {
        $admins = Admin::onlyTrashed()
            ->select(['id', 'name', 'email', 'role_id', 'is_active', 'deleted_at', 'created_at', 'updated_at'])
            ->with(['role:id,name,display_name'])
            ->latest()
            ->paginate(15);
        $superAdminCount = Admin::whereHas('role', function ($query) {
            $query->where('name', 'super_admin');
        })->count();

        return view('admin.admins.trash', compact('admins', 'superAdminCount'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $roles = Role::select(['id', 'name', 'display_name', 'description'])->orderBy('display_name')->get();
        return view('admin.admins.create', compact('roles'));
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'must_change_password' => true,
            'role_id' => $validated['role_id'],
            'is_active' => $request->has('is_active'),
            'email_verified_at' => now(),
        ]);

        CacheVersion::bump('roles');

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified admin.
     */
    public function show(Admin $admin)
    {
        $admin->load('role.permissions', 'loginLogs');
        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(Admin $admin)
    {
        // Prevent editing super admin by non-super admin
        if ($admin->isSuperAdmin() && !Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'You cannot edit a super admin account.');
        }

        $roles = Role::select(['id', 'name', 'display_name', 'description'])->orderBy('display_name')->get();
        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        // Prevent editing super admin by non-super admin
        if ($admin->isSuperAdmin() && !Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'You cannot edit a super admin account.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $admin->update($updateData);

        CacheVersion::bump('roles');

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(Admin $admin)
    {
        // Prevent deleting self
        if ($admin->id === Auth::guard('admin')->id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last remaining super admin
        if ($admin->isSuperAdmin()) {
            $superAdminCount = Admin::whereHas('role', function ($query) {
                $query->where('name', 'super_admin');
            })->count();

            if ($superAdminCount <= 1) {
                return redirect()->route('admin.admins.index')
                    ->with('error', 'At least one super admin account must remain.');
            }
        }

        $admin->delete();

        CacheVersion::bump('roles');

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }

    /**
     * Restore a soft-deleted admin.
     */
    public function restore(string $adminId)
    {
        $admin = Admin::onlyTrashed()->with('role')->findOrFail($adminId);

        if ($admin->isSuperAdmin() && !Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'You cannot restore a super admin account.');
        }

        $admin->restore();

        CacheVersion::bump('roles');

        return redirect()->route('admin.admins.trash')
            ->with('success', 'Admin restored successfully.');
    }

    /**
     * Permanently delete a soft-deleted admin.
     */
    public function forceDelete(string $adminId)
    {
        $admin = Admin::onlyTrashed()->with('role')->findOrFail($adminId);

        if ($admin->isSuperAdmin()) {
            $superAdminCount = Admin::whereHas('role', function ($query) {
                $query->where('name', 'super_admin');
            })->count();

            if ($superAdminCount === 0) {
                return redirect()->route('admin.admins.trash')
                    ->with('error', 'At least one super admin account must remain.');
            }
        }

        $admin->forceDelete();

        CacheVersion::bump('roles');

        return redirect()->route('admin.admins.trash')
            ->with('success', 'Admin permanently deleted.');
    }

    /**
     * Toggle admin active status.
     */
    public function toggleStatus(Admin $admin)
    {
        // Prevent deactivating self
        if ($admin->id === Auth::guard('admin')->id()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'You cannot deactivate your own account.'], 403);
            }
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $admin->update(['is_active' => !$admin->is_active]);

        CacheVersion::bump('roles');

        $status = $admin->is_active ? 'activated' : 'deactivated';
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'is_active' => $admin->is_active, 'message' => "Admin {$status} successfully."]);
        }
        
        return back()->with('success', "Admin {$status} successfully.");
    }
}
