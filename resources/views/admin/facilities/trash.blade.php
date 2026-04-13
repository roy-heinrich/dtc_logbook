@extends('admin.layouts.app')

@php
    $pageTitle = 'Facilities Trash';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-500 dark:text-slate-400">Deleted facilities</div>
            <div class="text-2xl font-semibold text-slate-900 dark:text-white">Facilities Trash</div>
        </div>
        <a href="{{ route('admin.facilities.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
            Back to Facilities
        </a>
    </div>

    <div class="rounded-xl glass-card shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Recently Deleted</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Facility</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Deleted At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($facilities as $facility)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">
                                {{ $facility->facility_name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $facility->deleted_at?->format('Y-m-d H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <div class="flex items-center gap-3">
                                    <form action="{{ route('admin.facilities.restore', $facility->facility_id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                            Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.facilities.force-delete', $facility->facility_id) }}" method="POST" class="inline" data-confirm-title="Delete Facility Permanently?" data-confirm-message="This will permanently delete the facility and cannot be undone." data-confirm-submit="Delete Permanently" data-confirm-type="permanent-delete">
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
                            <td colspan="3" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                No deleted facilities.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($facilities->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
