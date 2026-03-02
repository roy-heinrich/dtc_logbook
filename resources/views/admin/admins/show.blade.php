@extends('admin.layouts.app')

@php
    $pageTitle = 'Admin Details';
@endphp

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Admin Details</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">View administrator information and permissions</p>
        </div>
        <a href="{{ route('admin.admins.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
            Back to List
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Basic Information -->
        <div class="rounded-xl glass-card p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Basic Information</h2>
            <dl class="mt-4 space-y-3">
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Name</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $admin->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Email</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $admin->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Role</dt>
                    <dd class="mt-1 text-sm">
                        @if($admin->role)
                            <span class="inline-flex rounded-full bg-brand-100 px-2 py-1 text-xs font-medium text-brand-800 dark:bg-brand-900/30 dark:text-brand-100">
                                {{ $admin->role->display_name }}
                            </span>
                        @else
                            <span class="text-slate-400">No role assigned</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Status</dt>
                    <dd class="mt-1 text-sm">
                        @if($admin->is_active)
                            <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-100">Active</span>
                        @else
                            <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-100">Inactive</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Created</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $admin->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Permissions -->
        <div class="rounded-xl glass-card p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Permissions</h2>
            @if($admin->role && $admin->role->permissions->count() > 0)
                <ul class="mt-4 space-y-2">
                    @if($admin->isSuperAdmin())
                        <li class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium">All Permissions (Super Admin)</span>
                        </li>
                    @else
                        @foreach($admin->role->permissions as $permission)
                            <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $permission->display_name }}
                            </li>
                        @endforeach
                    @endif
                </ul>
            @else
                <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">No permissions assigned.</p>
            @endif
        </div>
    </div>

    <!-- Recent Login Logs -->
    <div class="rounded-xl glass-card overflow-hidden shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Recent Login Activity</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 rounded-tl-xl">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">User Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 rounded-tr-xl">Login Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($admin->loginLogs->take(5) as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-900 dark:text-white {{ $loop->last ? 'rounded-bl-xl' : '' }}">{{ $log->ip_address }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $log->user_agent }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300 {{ $loop->last ? 'rounded-br-xl' : '' }}">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                No login activity recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
