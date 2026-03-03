@extends('admin.layouts.app')

@php($pageTitle = 'Edit Activity')

@section('content')
    <div class="max-w-3xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="text-sm text-slate-500 dark:text-slate-400">Update activity details</div>
            <div class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">
                {{ $activity->user?->lname_user }}, {{ $activity->user?->fname_user }}
            </div>

            <form method="POST" action="{{ route('admin.activities.update', $activity) }}" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold uppercase text-slate-500">Facility Used</label>
                    <input type="text" name="facility_used" value="{{ old('facility_used', $activity->facility_used) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950" required>
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold uppercase text-slate-500">Service Type</label>
                    <input type="text" name="service_type" value="{{ old('service_type', $activity->service_type) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950" required>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Activity Date</label>
                    <input type="date" name="activity_date" value="{{ old('activity_date', $activity->activity_at?->timezone(config('app.timezone'))->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-500">Activity Time</label>
                    <input type="time" name="activity_time" value="{{ old('activity_time', $activity->activity_at?->timezone(config('app.timezone'))->format('H:i')) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                </div>

                <div class="md:col-span-2 flex items-center justify-between">
                    <a href="{{ route('admin.activities.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400">Back to list</a>
                    <button type="submit" class="rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
