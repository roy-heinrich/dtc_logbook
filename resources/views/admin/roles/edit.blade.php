@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit Role';
@endphp

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Role</h1>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Update role details and permissions</p>
    </div>

    <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-xl glass-card p-6 shadow-sm">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Role Name (Internal)</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Use lowercase and underscores only (e.g., content_editor)</p>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="display_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Display Name</label>
                    <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $role->display_name) }}" required
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Human-readable name shown to users</p>
                    @error('display_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Permissions</label>
                    <div class="mt-2 space-y-2 rounded-lg border border-slate-300 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950">
                        @foreach($permissions as $permission)
                            <div class="flex items-start">
                                <div class="flex h-5 items-center">
                                    <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}"
                                        {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-slate-300 text-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900">
                                </div>
                                <div class="ml-3">
                                    <label for="permission_{{ $permission->id }}" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        {{ $permission->display_name }}
                                    </label>
                                    @if($permission->description)
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $permission->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('permissions')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.roles.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                Cancel
            </a>
            <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">
                Update Role
            </button>
        </div>
    </form>
</div>
@endsection
