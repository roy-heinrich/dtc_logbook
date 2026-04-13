@extends('admin.layouts.app')

@php
    $pageTitle = 'Roles';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Roles</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.trash') }}" title="View Restore Items" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.05 11a9 9 0 0115.5-4.5L21 9m0-6v6h-6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.95 13a9 9 0 01-15.5 4.5L3 15m0 6v-6h6" />
                </svg>
            </a>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Role
            </a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($roles as $role)
            <div class="rounded-xl glass-card p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $role->display_name }}</h3>
                        @if($role->isSuperAdmin())
                            <span class="mt-1 inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-100">System Role</span>
                        @endif
                    </div>
                    @if(!$role->isSuperAdmin())
                        <div class="flex gap-2">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" data-confirm-title="Delete Role?" data-confirm-message="This role will be moved to trash and can be restored later." data-confirm-submit="Move to Trash">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                @if($role->description)
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $role->description }}</p>
                @endif

                <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4 dark:border-slate-800">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        <span class="font-medium text-slate-900 dark:text-white">{{ $role->admins_count }}</span> 
                        {{ Str::plural('admin', $role->admins_count) }}
                    </div>
                    <a href="{{ route('admin.roles.show', $role) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                        View Details →
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl glass-card p-12 text-center shadow-sm">
                <p class="text-sm text-slate-500 dark:text-slate-400">No roles found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
