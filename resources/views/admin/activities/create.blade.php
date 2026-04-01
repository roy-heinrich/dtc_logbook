@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-4">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Create Activity</h1>
            <a href="{{ route('admin.activities.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                ← Back to Activities
            </a>
        </div>

        <form action="{{ route('admin.activities.store') }}" method="POST" class="space-y-6 rounded-2xl glass-card p-6 shadow-sm">
            @csrf

            <!-- User Selection -->
            <div>
                <label class="text-xs font-semibold uppercase text-slate-500">User</label>
                <select name="user_id" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <option value="">Select a user...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->user_id }}" @selected(old('user_id') == $user->user_id)>
                            {{ $user->lname_user }}, {{ $user->fname_user }} {{ $user->mname_user ? substr($user->mname_user, 0, 1) . '.' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Facility Used -->
            <div>
                <label class="text-xs font-semibold uppercase text-slate-500">Facility Used</label>
                <input type="text" name="facility_used" value="{{ old('facility_used') }}" required placeholder="e.g., Computer Lab, Library" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                @error('facility_used')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Service Type -->
            <div>
                <label class="text-xs font-semibold uppercase text-slate-500">Service Type</label>
                <input type="text" name="service_type" value="{{ old('service_type') }}" required placeholder="e.g., Consultation, Training" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                @error('service_type')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Activity Date and Time -->
            <div class="grid grid-cols-2 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Activity Date</label>
                    <input type="date" name="activity_date" value="{{ old('activity_date') }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                    @error('activity_date')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Activity Time</label>
                    <input type="time" name="activity_time" value="{{ old('activity_time') }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                    @error('activity_time')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between gap-4 md:col-span-2">
                <a href="{{ route('admin.activities.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">Back to list</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Create Activity
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
