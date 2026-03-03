@extends('admin.layouts.app')

@php($pageTitle = 'Login Logs')

@section('content')
    <div class="rounded-2xl glass-card p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-900 dark:text-white">Filter Logins</div>
        <form method="GET" class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase text-slate-500">Registered User</label>
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

    <div class="mt-6 rounded-2xl glass-card shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-[640px] w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-400 dark:bg-slate-950">
                <tr>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Facility</th>
                    <th class="px-6 py-3">Service</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($logs as $log)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-950">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">
                                {{ $log->user?->lname_user }}, {{ $log->user?->fname_user }}
                            </div>
                            <div class="text-xs text-slate-400">#{{ $log->user_id }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $log->facility_used }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $log->service_type }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $log->activity_at?->timezone(config('app.timezone'))->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $log->activity_at?->timezone(config('app.timezone'))->format('H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
                            No login logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 rounded-2xl glass-card p-6 shadow-sm">
        {{ $logs->links() }}
    </div>
@endsection
