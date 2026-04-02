<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Support\CacheVersion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleManagementController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::withCount('admins')->latest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Display soft-deleted roles.
     */
    public function trash()
    {
        $roles = Role::onlyTrashed()->withCount('admins')->latest()->get();
        return view('admin.roles.trash', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles', 'alpha_dash'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        CacheVersion::bump('roles');

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'admins');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        // Prevent editing super admin role
        if ($role->isSuperAdmin()) {
            abort(403, 'Super admin role cannot be edited.');
        }

        $permissions = Permission::all();
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing super admin role
        if ($role->isSuperAdmin()) {
            abort(403, 'Super admin role cannot be edited.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id), 'alpha_dash'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        CacheVersion::bump('roles');

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting super admin role
        if ($role->isSuperAdmin()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super admin role cannot be deleted.');
        }

        // Check if role has admins
        if ($role->admins()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete role that has admins assigned to it.');
        }

        $role->delete();

        CacheVersion::bump('roles');

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Restore a soft-deleted role.
     */
    public function restore(string $roleId)
    {
        $role = Role::onlyTrashed()->findOrFail($roleId);

        if ($role->isSuperAdmin()) {
            return redirect()->route('admin.roles.trash')
                ->with('error', 'Super admin role cannot be restored.');
        }

        $role->restore();

        CacheVersion::bump('roles');

        return redirect()->route('admin.roles.trash')
            ->with('success', 'Role restored successfully.');
    }

    /**
     * Permanently delete a soft-deleted role.
     */
    public function forceDelete(string $roleId)
    {
        $role = Role::onlyTrashed()->findOrFail($roleId);

        if ($role->isSuperAdmin()) {
            return redirect()->route('admin.roles.trash')
                ->with('error', 'Super admin role cannot be deleted.');
        }

        $role->forceDelete();

        CacheVersion::bump('roles');

        return redirect()->route('admin.roles.trash')
            ->with('success', 'Role permanently deleted.');
    }
}
