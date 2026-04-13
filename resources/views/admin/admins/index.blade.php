@extends('admin.layouts.app')

@php
    $pageTitle = 'Manage Admins';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Manage Admins</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.admins.trash') }}" title="View Restore Items" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.05 11a9 9 0 0115.5-4.5L21 9m0-6v6h-6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.95 13a9 9 0 01-15.5 4.5L3 15m0 6v-6h6" />
                </svg>
            </a>
            <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Admin
            </a>
        </div>
    </div>

    <div class="rounded-xl glass-card shadow-sm">
        <!-- Mobile/Tablet View - Card Layout -->
        <div class="md:hidden space-y-3 p-4">
            @forelse ($admins as $admin)
                @php
                    $isLastSuperAdmin = $admin->isSuperAdmin() && $superAdminCount <= 1;
                    $canDeleteAdmin = $admin->id !== auth('admin')->id() && !$isLastSuperAdmin;
                @endphp
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800">
                    <div class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-semibold text-slate-600 dark:text-slate-300 text-sm">Name</span>
                            <div class="text-right">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $admin->name }}</div>
                                @if($admin->id === auth('admin')->id())
                                    <span class="text-xs text-slate-500">(You)</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-center gap-2 text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Email:</span>
                            <span class="font-medium text-slate-900 dark:text-white break-all text-right">{{ $admin->email }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2 text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Role:</span>
                            <div class="text-right">
                                @if($admin->role)
                                    <span class="inline-flex rounded-full bg-brand-100 px-2 py-1 text-xs font-medium text-brand-800 dark:bg-brand-900/30 dark:text-brand-100">
                                        {{ $admin->role->display_name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-sm">No role</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-center gap-2 text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Status:</span>
                            <div class="text-right">
                                <span
                                    data-status-pill
                                    data-active-class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100"
                                    data-inactive-class="inline-flex rounded-full bg-rose-100 px-2 py-1 text-xs font-medium text-rose-800 dark:bg-rose-900/30 dark:text-rose-100"
                                    class="{{ $admin->is_active ? 'inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100' : 'inline-flex rounded-full bg-rose-100 px-2 py-1 text-xs font-medium text-rose-800 dark:bg-rose-900/30 dark:text-rose-100' }}"
                                >
                                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center gap-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                            <span class="font-semibold text-slate-600 dark:text-slate-300 text-sm">Actions</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.admins.show', $admin) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(!$admin->isSuperAdmin() || auth('admin')->user()->isSuperAdmin())
                                    <a href="{{ route('admin.admins.edit', $admin) }}" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endif
                                @if($admin->id !== auth('admin')->id())
                                    <label class="toggle-admin-status inline-flex items-center cursor-pointer" 
                                           data-admin-id="{{ $admin->id }}"
                                           data-status-url="{{ route('admin.admins.toggle-status', $admin) }}"
                                           style="gap: 0.5rem;">
                                        <input type="checkbox" class="sr-only peer" {{ $admin->is_active ? 'checked' : '' }} style="display: none;" />
                                        <div style="position: relative; width: 3rem; height: 1.5rem; background-color: #e2e8f0; border-radius: 9999px; border: 2px solid #cbd5e1; transition: all 0.3s ease; cursor: pointer;" class="toggle-switch" data-toggle>
                                            <div style="position: absolute; left: 0.2rem; top: 50%; width: 1.1rem; height: 1.1rem; background-color: #94a3b8; border-radius: 50%; transition: all 0.3s ease; content: '';" class="toggle-dot"></div>
                                        </div>
                                    </label>
                                @endif
                                @if($canDeleteAdmin)
                                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline" data-confirm-title="Delete Admin?" data-confirm-message="This admin account will be moved to trash and can be restored later." data-confirm-submit="Move to Trash">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @elseif($isLastSuperAdmin)
                                    <button type="button" class="cursor-not-allowed text-slate-400" title="At least one super admin must remain." disabled aria-disabled="true">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                    No admins found.
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
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 rounded-tr-xl">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($admins as $admin)
                        @php
                            $isLastSuperAdmin = $admin->isSuperAdmin() && $superAdminCount <= 1;
                            $canDeleteAdmin = $admin->id !== auth('admin')->id() && !$isLastSuperAdmin;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900 dark:text-white {{ $loop->last ? 'rounded-bl-xl' : '' }}">
                                {{ $admin->name }}
                                @if($admin->id === auth('admin')->id())
                                    <span class="ml-2 text-xs text-slate-500">(You)</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $admin->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                @if($admin->role)
                                    <span class="inline-flex rounded-full bg-brand-100 px-2 py-1 text-xs font-medium text-brand-800 dark:bg-brand-900/30 dark:text-brand-100">
                                        {{ $admin->role->display_name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">No role</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span
                                    data-status-pill
                                    data-active-class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100"
                                    data-inactive-class="inline-flex rounded-full bg-rose-100 px-2 py-1 text-xs font-medium text-rose-800 dark:bg-rose-900/30 dark:text-rose-100"
                                    class="{{ $admin->is_active ? 'inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100' : 'inline-flex rounded-full bg-rose-100 px-2 py-1 text-xs font-medium text-rose-800 dark:bg-rose-900/30 dark:text-rose-100' }}"
                                >
                                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm {{ $loop->last ? 'rounded-br-xl' : '' }}">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.admins.show', $admin) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if(!$admin->isSuperAdmin() || auth('admin')->user()->isSuperAdmin())
                                        <a href="{{ route('admin.admins.edit', $admin) }}" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($admin->id !== auth('admin')->id())
                                        <label class="toggle-admin-status inline-flex items-center cursor-pointer" 
                                               data-admin-id="{{ $admin->id }}"
                                               data-status-url="{{ route('admin.admins.toggle-status', $admin) }}"
                                               style="gap: 0.5rem;">
                                            <input type="checkbox" class="sr-only peer" {{ $admin->is_active ? 'checked' : '' }} style="display: none;" />
                                            <div style="position: relative; width: 3rem; height: 1.5rem; background-color: #e2e8f0; border-radius: 9999px; border: 2px solid #cbd5e1; transition: all 0.3s ease; cursor: pointer;" class="toggle-switch" data-toggle>
                                                <div style="position: absolute; left: 0.2rem; top: 50%; width: 1.1rem; height: 1.1rem; background-color: #94a3b8; border-radius: 50%; transition: all 0.3s ease; content: '';" class="toggle-dot"></div>
                                            </div>
                                        </label>
                                    @endif
                                    @if($canDeleteAdmin)
                                        <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline" data-confirm-title="Delete Admin?" data-confirm-message="This admin account will be moved to trash and can be restored later." data-confirm-submit="Move to Trash">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @elseif($isLastSuperAdmin)
                                        <button type="button" class="cursor-not-allowed text-slate-400" title="At least one super admin must remain." disabled aria-disabled="true">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                No admins found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @if($admins->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                {{ $admins->links() }}
            </div>
        @endif
    </div>

    <div class="rounded-2xl glass-card p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-900 dark:text-white">Recent Admin Logins</div>
        <div class="mt-4 space-y-4">
            @forelse ($recentLogins as $log)
                <div class="flex items-center justify-between rounded-xl border border-slate-100/50 bg-white/40 dark:bg-slate-800/40 px-4 py-3 text-sm text-slate-700 dark:border-slate-700/50 dark:text-slate-200">
                    <div>
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $log->user?->name ?? 'Unknown User' }}</div>
                        <div class="text-xs text-slate-600 dark:text-slate-400">{{ $log->user?->email }}</div>
                    </div>
                    <div class="text-right text-xs text-slate-600 dark:text-slate-400">
                        <div>{{ $log->login_at?->format('Y-m-d H:i') }}</div>
                        <div class="truncate">{{ $log->ip_address }}</div>
                    </div>
                </div>
            @empty
                <div class="text-sm text-slate-600 dark:text-slate-400">No login logs recorded yet.</div>
            @endforelse
        </div>
        @if($recentLogins->hasPages())
            <div class="mt-4 border-t border-slate-200 pt-4 dark:border-slate-800">
                {{ $recentLogins->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-admin-status').forEach((label) => {
        const checkbox = label.querySelector('input[type="checkbox"]');
        const toggleSwitch = label.querySelector('[data-toggle]');
        const toggleDot = label.querySelector('[class*="toggle-dot"]');

        function updateToggleAppearance() {
            if (checkbox.checked) {
                toggleSwitch.style.backgroundColor = '#3b82f6';
                toggleSwitch.style.borderColor = '#3b82f6';
                toggleDot.style.transform = 'translateX(1.5rem) translateY(-50%)';
                toggleDot.style.backgroundColor = '#ffffff';
            } else {
                toggleSwitch.style.backgroundColor = '#e2e8f0';
                toggleSwitch.style.borderColor = '#cbd5e1';
                toggleDot.style.transform = 'translateY(-50%)';
                toggleDot.style.backgroundColor = '#94a3b8';
            }
        }

        // Set initial state
        updateToggleAppearance();

        checkbox.addEventListener('change', async (event) => {
            event.preventDefault();

            const url = label.getAttribute('data-status-url');
            const row = label.closest('tr');
            const statusPill = row ? row.querySelector('[data-status-pill]') : null;
            const wasChecked = !checkbox.checked;

            checkbox.disabled = true;
            label.style.pointerEvents = 'none';
            label.style.opacity = '0.6';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Status update failed.');
                }

                updateToggleAppearance();

                // Update status pill if it exists
                if (statusPill) {
                    const activeClass = statusPill.getAttribute('data-active-class');
                    const inactiveClass = statusPill.getAttribute('data-inactive-class');

                    if (data.is_active) {
                        statusPill.className = activeClass;
                        statusPill.textContent = 'Active';
                    } else {
                        statusPill.className = inactiveClass;
                        statusPill.textContent = 'Inactive';
                    }
                }
            } catch (error) {
                // Revert checkbox to previous state
                checkbox.checked = wasChecked;
                updateToggleAppearance();
                alert(error.message || 'Unable to update admin status right now.');
                console.error(error);
            } finally {
                checkbox.disabled = false;
                label.style.pointerEvents = 'auto';
                label.style.opacity = '1';
            }
        });
    });
</script>
@endpush
