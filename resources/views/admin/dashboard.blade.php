@extends('admin.layouts.app')

@php($pageTitle = 'Dashboard')

@section('content')
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl glass-card p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-widest text-slate-600 dark:text-slate-400">Total Users</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalUsers) }}</div>
        </div>
        <div class="rounded-2xl glass-card p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-widest text-slate-600 dark:text-slate-400">Total Activities</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalActivities) }}</div>
        </div>
        <div class="rounded-2xl glass-card p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-widest text-slate-600 dark:text-slate-400">Today</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($todayActivities) }}</div>
        </div>
        <div class="rounded-2xl glass-card p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-widest text-slate-600 dark:text-slate-400">Latest Activity</div>
            <div class="mt-3 text-sm text-slate-800 dark:text-slate-300">
                @if ($latestActivity)
                    <div class="font-semibold text-slate-900 dark:text-white">
                        {{ $latestActivity->user?->lname_user }}, {{ $latestActivity->user?->fname_user }}
                    </div>
                    <div class="text-slate-700 dark:text-slate-400">{{ $latestActivity->activity_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</div>
                @else
                    No activity data available
                @endif
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl glass-card p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900 dark:text-white">Activity Volume</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Last 7 days</div>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-7 items-end gap-3">
                @foreach ($activityChart as $point)
                    <div class="flex flex-col items-center gap-2">
                        <div class="flex h-28 w-full items-end">
                            <div class="w-full rounded-lg bg-brand-500/80" style="height: {{ $point['percent'] }}%"></div>
                        </div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                            {{ \Illuminate\Support\Carbon::parse($point['date'])->format('D') }}
                        </div>
                        <div class="text-[11px] text-slate-400">{{ $point['total'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl glass-card p-6 shadow-sm">
                <div class="text-sm font-semibold text-slate-900 dark:text-white">Top Facilities</div>
                <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                    @forelse ($topFacilities as $facility)
                        <div class="flex items-center justify-between">
                            <span>{{ $facility->facility_used }}</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $facility->total }}</span>
                        </div>
                    @empty
                        <div>No facilities recorded</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl glass-card p-6 shadow-sm">
                <div class="text-sm font-semibold text-slate-900 dark:text-white">Top Service Types</div>
                <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                    @forelse ($topServices as $service)
                        <div class="flex items-center justify-between">
                            <span>{{ $service->service_type }}</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $service->total }}</span>
                        </div>
                    @empty
                        <div>No service data available</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
