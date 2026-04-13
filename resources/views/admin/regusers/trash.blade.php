@extends('admin.layouts.app')

@php($pageTitle = 'Users Trash')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-500 dark:text-slate-400">Deleted registered users</div>
            <div class="text-2xl font-semibold text-slate-900 dark:text-white">Users Trash</div>
        </div>
        <a href="{{ route('admin.regusers.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
            Back to Users
        </a>
    </div>

    <div class="rounded-2xl glass-card shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-400 dark:bg-slate-950">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-xl">Name</th>
                        <th class="px-4 py-3">Sector</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Deleted At</th>
                        <th class="px-4 py-3 rounded-tr-xl">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-950">
                            <td class="px-4 py-4 font-semibold text-slate-900 dark:text-white {{ $loop->last ? 'rounded-bl-xl' : '' }}">
                                {{ $user->lname_user }}, {{ $user->fname_user }}
                            </td>
                            <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->sector_user }}</td>
                            <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->number_user }}</td>
                            <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->deleted_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-4 text-right {{ $loop->last ? 'rounded-br-xl' : '' }}">
                                <div class="flex items-center justify-end gap-3">
                                    <form action="{{ route('admin.regusers.restore', $user->user_id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                            Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.regusers.force-delete', $user->user_id) }}" method="POST" class="inline" data-confirm-title="Delete User Permanently?" data-confirm-message="This will permanently delete the user record and cannot be undone." data-confirm-submit="Delete Permanently" data-confirm-type="permanent-delete">
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
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                No deleted users.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
