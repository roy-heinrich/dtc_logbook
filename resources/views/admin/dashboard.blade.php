@extends('admin.layouts.app')

@php($pageTitle = 'Dashboard')

@section('content')
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg glass-card p-5">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Users</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalUsers) }}</div>
        </div>
        <div class="rounded-lg glass-card p-5">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Activities</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalActivities) }}</div>
        </div>
        <div class="rounded-lg glass-card p-5">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Today</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($todayActivities) }}</div>
        </div>
        <div class="rounded-lg glass-card p-5">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Latest Activity</div>
            <div class="mt-3 text-sm text-slate-800 dark:text-slate-300">
                @if ($latestActivity)
                    <div class="font-semibold text-slate-900 dark:text-white">
                        {{ $latestActivity->user?->lname_user }}, {{ $latestActivity->user?->fname_user }}
                    </div>
                    <div class="text-slate-600 dark:text-slate-400">{{ $latestActivity->activity_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</div>
                @else
                    No activity data available
                @endif
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-lg glass-card p-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-6">Activity Volume (Last 7 days)</h3>
            <div class="relative h-64 md:h-72">
                <canvas id="activityVolumeChart"></canvas>
            </div>
        </div>

        <div class="rounded-lg glass-card p-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Most Active Users</h3>
            @if ($mostActiveUsers->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($mostActiveUsers as $user)
                        <div class="flex items-start justify-between gap-3">
                            <span class="min-w-0 flex-1 break-words text-sm text-slate-700 dark:text-slate-300">{{ $user['name'] }}</span>
                            <span class="shrink-0 text-sm font-semibold text-slate-900 dark:text-white">{{ $user['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No activity data</div>
            @endif
        </div>
    </div>

    <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-lg glass-card p-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Gender Distribution</h3>
            @if ($genderStats->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <div class="relative w-28 h-28 flex-shrink-0">
                        <canvas id="genderDistributionChart"></canvas>
                    </div>
                    <div class="flex-1 space-y-2 w-full">
                        @foreach ($genderChartData['slices'] as $slice)
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-3 h-3 rounded flex-shrink-0" style="background-color: {{ $slice['color'] }}"></div>
                                    <span class="text-slate-700 dark:text-slate-300">{{ $slice['label'] === 'M' ? 'Male' : 'Female' }}</span>
                                </div>
                                <span class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">{{ $slice['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No user data</div>
            @endif
        </div>

        <div class="rounded-lg glass-card p-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Top Facilities</h3>
            @if ($topFacilities->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <div class="relative w-28 h-28 flex-shrink-0">
                        <canvas id="topFacilitiesChart"></canvas>
                    </div>
                    <div class="flex-1 space-y-2 w-full">
                        @foreach ($facilityChartData['slices'] as $slice)
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-3 h-3 rounded flex-shrink-0" style="background-color: {{ $slice['color'] }}"></div>
                                    <span class="text-slate-700 dark:text-slate-300 break-words">{{ $slice['label'] }}</span>
                                </div>
                                <span class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">{{ $slice['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No facilities recorded</div>
            @endif
        </div>

        <div class="rounded-lg glass-card p-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Top Service Types</h3>
            @if ($topServices->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <div class="relative w-28 h-28 flex-shrink-0">
                        <canvas id="topServicesChart"></canvas>
                    </div>
                    <div class="flex-1 space-y-2 w-full">
                        @foreach ($serviceChartData['slices'] as $slice)
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-3 h-3 rounded flex-shrink-0" style="background-color: {{ $slice['color'] }}"></div>
                                    <span class="text-slate-700 dark:text-slate-300 break-words">{{ $slice['label'] }}</span>
                                </div>
                                <span class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">{{ $slice['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No service data available</div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Chart) {
                return;
            }

            const isDark = document.documentElement.classList.contains('dark');
            const axisColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.2)' : 'rgba(100, 116, 139, 0.18)';

            const activityCanvas = document.getElementById('activityVolumeChart');
            if (activityCanvas) {
                const activityPoints = @json($activityChart);
                const labels = activityPoints.map((point) => {
                    const chartDate = new Date(point.date + 'T00:00:00');
                    const now = new Date();
                    const isToday =
                        chartDate.getFullYear() === now.getFullYear() &&
                        chartDate.getMonth() === now.getMonth() &&
                        chartDate.getDate() === now.getDate();

                    const day = chartDate.toLocaleDateString('en-US', { weekday: 'short' });
                    return isToday ? `Today · ${day}` : day;
                });
                const totals = activityPoints.map((point) => Number(point.total));

                new window.Chart(activityCanvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data: totals,
                            borderRadius: 6,
                            backgroundColor: isDark ? '#3b82f6' : '#2563eb',
                            maxBarThickness: 36,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1200,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: axisColor,
                                },
                                grid: {
                                    display: false,
                                },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    color: axisColor,
                                },
                                grid: {
                                    color: gridColor,
                                },
                            },
                        },
                    },
                });
            }

            const genderCanvas = document.getElementById('genderDistributionChart');
            if (genderCanvas) {
                const genderSlices = @json($genderChartData['slices']);
                const genderLabels = genderSlices.map((slice) => slice.label === 'M' ? 'Male' : 'Female');
                const genderValues = genderSlices.map((slice) => Number(slice.value));
                const genderColors = genderSlices.map((slice) => slice.color);

                new window.Chart(genderCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: genderLabels,
                        datasets: [{
                            data: genderValues,
                            backgroundColor: genderColors,
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1200,
                            easing: 'easeInOutQuart'
                        },
                        cutout: '62%',
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                    },
                });
            }

            const facilitiesCanvas = document.getElementById('topFacilitiesChart');
            if (facilitiesCanvas) {
                const facilitySlices = @json($facilityChartData['slices']);
                const facilityLabels = facilitySlices.map((slice) => slice.label);
                const facilityValues = facilitySlices.map((slice) => Number(slice.value));
                const facilityColors = facilitySlices.map((slice) => slice.color);

                new window.Chart(facilitiesCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: facilityLabels,
                        datasets: [{
                            data: facilityValues,
                            backgroundColor: facilityColors,
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1200,
                            easing: 'easeInOutQuart'
                        },
                        cutout: '62%',
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                    },
                });
            }

            const servicesCanvas = document.getElementById('topServicesChart');
            if (servicesCanvas) {
                const serviceSlices = @json($serviceChartData['slices']);
                const serviceLabels = serviceSlices.map((slice) => slice.label);
                const serviceValues = serviceSlices.map((slice) => Number(slice.value));
                const serviceColors = serviceSlices.map((slice) => slice.color);

                new window.Chart(servicesCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: serviceLabels,
                        datasets: [{
                            data: serviceValues,
                            backgroundColor: serviceColors,
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1200,
                            easing: 'easeInOutQuart'
                        },
                        cutout: '62%',
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                    },
                });
            }
        });
    </script>
@endpush
