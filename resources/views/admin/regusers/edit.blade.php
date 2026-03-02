@extends('admin.layouts.app')

@php($pageTitle = 'Edit User')

@section('content')
    <div class="max-w-3xl">
        <div class="rounded-2xl glass-card p-6 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">Update registered user information</div>
            <div class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">
                {{ $user->lname_user }}, {{ $user->fname_user }}
            </div>

            <form method="POST" action="{{ route('admin.regusers.update', $user) }}" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">First Name</label>
                    <input type="text" name="fname_user" value="{{ old('fname_user', $user->fname_user) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Last Name</label>
                    <input type="text" name="lname_user" value="{{ old('lname_user', $user->lname_user) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950" required>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Middle Name</label>
                    <input type="text" name="mname_user" value="{{ old('mname_user', $user->mname_user) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Suffix</label>
                    <input type="text" name="suffix_user" value="{{ old('suffix_user', $user->suffix_user) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Birthdate</label>
                    <div class="relative mt-2">
                        <input
                            type="date"
                            name="birthdate"
                            value="{{ old('birthdate', $user->birthdate?->format('Y-m-d')) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 pr-10 text-sm dark:border-slate-800 dark:bg-slate-950 [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:h-full"
                        >
                        <button type="button" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Sex</label>
                    <input type="text" name="sex_user" value="{{ old('sex_user', $user->sex_user) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Sector</label>
                    <input type="text" name="sector_user" value="{{ old('sector_user', $user->sector_user) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Contact Number</label>
                    <input type="text" name="number_user" value="{{ old('number_user', $user->number_user) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                </div>

                <div class="md:col-span-2 flex items-center justify-between">
                    <a href="{{ route('admin.regusers.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400">Back to list</a>
                    <div class="flex items-center gap-2">
                        <button type="button" id="deleteUserBtn" class="rounded-xl border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/20">
                            Move to Trash
                        </button>
                        <button type="submit" class="rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 dark:bg-black/70">
        <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-lg dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/30">
                    <svg class="h-6 w-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Move User to Trash</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        Are you sure you want to move <strong>{{ $user->lname_user }}, {{ $user->fname_user }}</strong> to trash? You can restore the user later.
                    </p>
                    <p class="mt-3 text-xs text-rose-600 dark:text-rose-400">
                        ⚠ Warning: Activity logs will remain but the user will be marked as deleted.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" id="cancelDeleteBtn" class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" action="{{ route('admin.regusers.destroy', $user) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                        Move to Trash
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const deleteUserBtn = document.getElementById('deleteUserBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const deleteModal = document.getElementById('deleteModal');

    deleteUserBtn?.addEventListener('click', () => {
        deleteModal.classList.remove('hidden');
    });

    cancelDeleteBtn?.addEventListener('click', () => {
        deleteModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    deleteModal?.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
            deleteModal.classList.add('hidden');
        }
    });
</script>
@endpush
