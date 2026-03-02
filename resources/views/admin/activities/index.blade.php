@extends('admin.layouts.app')

@php($pageTitle = 'Activities')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="text-sm font-semibold text-slate-900 dark:text-white">Filter Activities</div>
        <form method="GET" class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase text-slate-500">User</label>
                <select name="user_id" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <option value="">All users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->user_id }}" @selected($filters['user_id'] == $user->user_id)>
                            {{ $user->lname_user }}, {{ $user->fname_user }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-slate-500">Facility</label>
                <select name="facility_used" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <option value="">All facilities</option>
                    @foreach ($facilities as $facility)
                        <option value="{{ $facility }}" @selected($filters['facility_used'] === $facility)>
                            {{ $facility }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-slate-500">Service</label>
                <select name="service_type" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <option value="">All services</option>
                    @foreach ($serviceTypes as $serviceType)
                        <option value="{{ $serviceType }}" @selected($filters['service_type'] === $serviceType)>
                            {{ $serviceType }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-slate-500">From</label>
                <input type="date" name="from" value="{{ $filters['from'] }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-slate-500">To</label>
                <input type="date" name="to" value="{{ $filters['to'] }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-800 dark:bg-slate-950">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl glass-card shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-400 dark:bg-slate-950">
                <tr>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Facility</th>
                    <th class="px-6 py-3">Service</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Time</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($activities as $activity)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-950">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">
                                {{ $activity->user?->lname_user }}, {{ $activity->user?->fname_user }}
                            </div>
                            <div class="text-xs text-slate-400">#{{ $activity->user_id }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $activity->facility_used }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $activity->service_type }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $activity->activity_date?->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $activity->activity_time }}</td>
                        <td class="px-6 py-4 text-right">
                            <a
                                href="{{ route('admin.activities.edit', $activity) }}"
                                class="rounded-lg border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-700 dark:text-brand-200 dark:hover:bg-brand-900/40"
                            >
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
                            No activities found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $activities->links() }}
    </div>
@endsection
