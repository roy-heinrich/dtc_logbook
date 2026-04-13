@extends('admin.layouts.app')

@php
    $pageTitle = 'Roles Trash';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-500 dark:text-slate-400">Deleted roles</div>
            <div class="text-2xl font-semibold text-slate-900 dark:text-white">Roles Trash</div>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
            Back to Roles
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($roles as $role)
            <div class="rounded-xl glass-card p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $role->display_name }}</h3>
                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Deleted {{ $role->deleted_at?->format('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>

                @if($role->description)
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $role->description }}</p>
                @endif

                <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4 dark:border-slate-800">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        <span class="font-medium text-slate-900 dark:text-white">{{ $role->admins_count }}</span>
                        {{ Str::plural('admin', $role->admins_count) }}
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <form action="{{ route('admin.roles.restore', $role->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                Restore
                            </button>
                        </form>
                        <form action="{{ route('admin.roles.force-delete', $role->id) }}" method="POST" class="inline" data-confirm-title="Delete Role Permanently?" data-confirm-message="This will permanently delete the role and cannot be undone." data-confirm-submit="Delete Permanently" data-confirm-type="permanent-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                Delete Permanently
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl glass-card p-12 text-center shadow-sm">
                <p class="text-sm text-slate-500 dark:text-slate-400">No roles in trash.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
