@extends('admin.layouts.app')

@php
    $pageTitle = 'Admins Trash';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-500 dark:text-slate-400">Deleted admin accounts</div>
            <div class="text-2xl font-semibold text-slate-900 dark:text-white">Admins Trash</div>
        </div>
        <a href="{{ route('admin.admins.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
            Back to Admins
        </a>
    </div>

    <div class="rounded-xl glass-card shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Deleted At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($admins as $admin)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">
                                {{ $admin->name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $admin->email }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                @if($admin->role)
                                    <span class="inline-flex rounded-full bg-brand-100 px-2 py-1 text-xs font-medium text-brand-800 dark:bg-brand-900/30 dark:text-brand-100">
                                        {{ $admin->role->display_name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">No role</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $admin->deleted_at?->format('Y-m-d H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <div class="flex items-center gap-3">
                                    <form action="{{ route('admin.admins.restore', $admin->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                            Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.admins.force-delete', $admin->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this admin permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                No deleted admins.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($admins->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
