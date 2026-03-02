@extends('admin.layouts.app')

@php
    $pageTitle = 'Role Details';
@endphp

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $role->display_name }}</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">View role information and permissions</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
            Back to List
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Role Information -->
        <div class="rounded-xl glass-card p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Role Information</h2>
            <dl class="mt-4 space-y-3">
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Display Name</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $role->display_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Internal Name</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $role->name }}</dd>
                </div>
                @if($role->description)
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Description</dt>
                        <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $role->description }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Type</dt>
                    <dd class="mt-1 text-sm">
                        @if($role->isSuperAdmin())
                            <span class="inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-100">System Role</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-800 dark:bg-slate-800 dark:text-slate-100">Custom Role</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Admins</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $role->admins->count() }}</dd>
                </div>
            </dl>
        </div>

        <!-- Permissions -->
        <div class="rounded-xl glass-card p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Permissions</h2>
            @if($role->permissions->count() > 0)
                <ul class="mt-4 space-y-2">
                    @foreach($role->permissions as $permission)
                        <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <div class="font-medium text-slate-900 dark:text-white">{{ $permission->display_name }}</div>
                                @if($permission->description)
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $permission->description }}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">No permissions assigned to this role.</p>
            @endif
        </div>
    </div>

    <!-- Admins with this Role -->
    <div class="rounded-xl glass-card shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Admins with this Role</h2>
        </div>

        <!-- Mobile/Tablet View -->
        <div class="md:hidden divide-y divide-slate-200 dark:divide-slate-800">
            @forelse ($role->admins as $admin)
                <div class="space-y-3 px-4 py-4">
                    <div class="flex justify-between items-start gap-2 pb-3 border-b border-slate-200 dark:border-slate-700">
                        <span class="font-semibold text-slate-500 dark:text-slate-400 text-sm">Name</span>
                        <span class="text-right font-medium text-slate-900 dark:text-white">{{ $admin->name }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-2">
                        <span class="font-semibold text-slate-500 dark:text-slate-400 text-sm">Email</span>
                        <span class="text-right text-slate-600 dark:text-slate-300 text-sm break-all">{{ $admin->email }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-2">
                        <span class="font-semibold text-slate-500 dark:text-slate-400 text-sm">Status</span>
                        <div class="text-right">
                            @if($admin->is_active)
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-100">Active</span>
                            @else
                                <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-100">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-end pt-2 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('admin.admins.show', $admin) }}" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300 text-sm font-medium">
                            View Details →
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    No admins assigned to this role.
                </div>
            @endforelse
        </div>

        <!-- Desktop View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 rounded-tl-xl">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 rounded-tr-xl">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($role->admins as $admin)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900 dark:text-white {{ $loop->last ? 'rounded-bl-xl' : '' }}">{{ $admin->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $admin->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                @if($admin->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-100">Active</span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-100">Inactive</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm {{ $loop->last ? 'rounded-br-xl' : '' }}">
                                <a href="{{ route('admin.admins.show', $admin) }}" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                    View Details →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                No admins assigned to this role.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
