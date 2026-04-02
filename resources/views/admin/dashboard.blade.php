@extends('admin.layouts.app')

@php($pageTitle = 'Dashboard')

@section('content')
    <div class="grid gap-4 sm:gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg glass-card p-5 h-full min-h-[132px] flex flex-col justify-between">
            <div class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Users</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalUsers) }}</div>
        </div>
        <div class="rounded-lg glass-card p-5 h-full min-h-[132px] flex flex-col justify-between">
            <div class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Activities</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white" data-dashboard-total-activities>{{ number_format($totalActivities) }}</div>
        </div>
        <div class="rounded-lg glass-card p-5 h-full min-h-[132px] flex flex-col justify-between">
            <div class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Today</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white" data-dashboard-today-activities>{{ number_format($todayActivities) }}</div>
        </div>
        <div class="rounded-lg glass-card latest-activity-card p-5 h-full min-h-[132px] flex flex-col justify-between">
            <div class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Latest Activity</div>
            <div class="mt-3 text-sm md:text-base text-slate-800 dark:text-slate-300">
                @if ($latestActivity)
                    <div class="font-semibold text-slate-900 dark:text-white latest-activity-name">
                        {{ $latestActivity->user?->lname_user }}, {{ $latestActivity->user?->fname_user }}
                    </div>
                    <div class="text-slate-600 dark:text-slate-400 latest-activity-time">{{ $latestActivity->activity_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</div>
                @else
                    No activity data available
                @endif
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:gap-6 lg:grid-cols-3 items-stretch">
        <div class="lg:col-span-2 rounded-lg glass-card p-4 sm:p-6 overflow-hidden h-full">
            <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white mb-6">Activity Volume (Last 7 days)</h3>
            <div class="relative h-56 sm:h-64 md:h-72">
                <canvas id="activityVolumeChart"></canvas>
            </div>
        </div>

        <div class="rounded-lg glass-card p-4 sm:p-6 h-full min-h-[320px] sm:min-h-[352px] flex flex-col">
            <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white mb-4">Most Active Users</h3>
            @if ($mostActiveUsers->isNotEmpty())
                <div class="space-y-3 overflow-y-auto flex-1 pr-1">
                    @foreach ($mostActiveUsers as $user)
                        <div class="flex items-start justify-between gap-3 min-w-0">
                            <span class="min-w-0 flex-1 break-words text-sm md:text-base text-slate-700 dark:text-slate-300">{{ $user['name'] }}</span>
                            <span class="shrink-0 text-sm md:text-base font-semibold text-slate-900 dark:text-white">{{ $user['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No activity data</div>
            @endif
        </div>
    </div>

    <div class="mt-6 grid items-stretch gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg glass-card p-5 h-full min-h-[220px] flex flex-col overflow-hidden">
            <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white mb-3">Gender Distribution</h3>
            @if ($genderStats->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-3 items-center sm:items-start flex-1 min-h-0">
                    <div class="relative w-28 h-28 sm:w-24 sm:h-24 md:w-28 md:h-28 max-w-full flex-shrink-0">
                        <canvas id="genderDistributionChart"></canvas>
                    </div>
                    <div class="flex-1 space-y-1 w-full min-w-0 max-h-40 overflow-y-auto pr-1">
                        @foreach ($genderChartData['slices'] as $slice)
                            <div class="flex items-center justify-between gap-2 text-sm md:text-base">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-3 h-3 rounded flex-shrink-0" style="background-color: {{ $slice['color'] }}"></div>
                                    <span class="text-slate-700 dark:text-slate-300 break-words">{{ $slice['label'] === 'M' ? 'Male' : 'Female' }}</span>
                                </div>
                                <span class="font-semibold text-slate-900 dark:text-white whitespace-nowrap flex-shrink-0">{{ $slice['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No user data</div>
            @endif
        </div>

        <div id="top-facilities-card" class="relative rounded-lg glass-card p-5 h-full min-h-[220px] flex flex-col overflow-hidden">
            <div data-card-content class="contents">
            <div class="mb-3 flex items-start justify-between gap-2">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Top Facilities</h3>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-1 text-xs" data-dashboard-filter>
                    <input type="hidden" name="service_year" value="{{ $serviceFilter['year'] }}">
                    <input type="hidden" name="service_month" value="{{ $serviceFilter['month'] }}">
                    <input type="hidden" name="training_year" value="{{ $trainingFilter['year'] }}">
                    <input type="hidden" name="training_month" value="{{ $trainingFilter['month'] }}">
                    <select name="facility_year" class="rounded border-slate-300 bg-white px-2 py-1 text-xs dark:border-slate-600 dark:bg-slate-800">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" @selected((int) $facilityFilter['year'] === (int) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    <select name="facility_month" class="rounded border-slate-300 bg-white px-2 py-1 text-xs dark:border-slate-600 dark:bg-slate-800">
                        <option value="">All months</option>
                        @foreach ($months as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((int) $facilityFilter['month'] === (int) $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @if ($topFacilities->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-start flex-1 min-h-0">
                    <div class="relative w-28 h-28 sm:w-24 sm:h-24 md:w-28 md:h-28 max-w-full flex-shrink-0">
                        <canvas id="topFacilitiesChart" data-slices='@json($facilityChartData['slices'])'></canvas>
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <div class="h-36 overflow-y-scroll scroll-container dashboard-scroll">
                            <div class="pr-6 space-y-2">
                            @foreach ($facilityChartData['slices'] as $slice)
                                <div class="flex justify-between items-center gap-3 text-sm md:text-base">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <div class="w-3 h-3 rounded flex-shrink-0" style="background-color: {{ $slice['color'] }}"></div>
                                        <span class="text-slate-700 dark:text-slate-300 truncate">{{ $slice['label'] }}</span>
                                    </div>
                                    <span class="font-semibold text-slate-900 dark:text-white whitespace-nowrap shrink-0 pr-1 pl-2">{{ $slice['value'] }}</span>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No facilities recorded</div>
            @endif
            </div>
            <div data-loading-overlay class="absolute inset-0 hidden items-center justify-center bg-slate-900/35 backdrop-blur-[1px]">
                <div class="flex flex-col items-center justify-center gap-2 rounded-md bg-white/90 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm dark:bg-slate-800/90 dark:text-slate-200">
                    <div class="newtons-cradle" aria-hidden="true">
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                    </div>
                    Please wait...
                </div>
            </div>
        </div>

        <div id="top-services-card" class="relative rounded-lg glass-card p-5 h-full min-h-[220px] flex flex-col overflow-hidden">
            <div data-card-content class="contents">
            <div class="mb-3 flex items-start justify-between gap-2">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Top Service Types</h3>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-1 text-xs" data-dashboard-filter>
                    <input type="hidden" name="facility_year" value="{{ $facilityFilter['year'] }}">
                    <input type="hidden" name="facility_month" value="{{ $facilityFilter['month'] }}">
                    <input type="hidden" name="training_year" value="{{ $trainingFilter['year'] }}">
                    <input type="hidden" name="training_month" value="{{ $trainingFilter['month'] }}">
                    <select name="service_year" class="rounded border-slate-300 bg-white px-2 py-1 text-xs dark:border-slate-600 dark:bg-slate-800">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" @selected((int) $serviceFilter['year'] === (int) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    <select name="service_month" class="rounded border-slate-300 bg-white px-2 py-1 text-xs dark:border-slate-600 dark:bg-slate-800">
                        <option value="">All months</option>
                        @foreach ($months as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((int) $serviceFilter['month'] === (int) $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @if ($topServices->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-start flex-1 min-h-0">
                    <div class="relative w-28 h-28 sm:w-24 sm:h-24 md:w-28 md:h-28 max-w-full flex-shrink-0">
                        <canvas id="topServicesChart" data-slices='@json($serviceChartData['slices'])'></canvas>
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <div class="h-36 overflow-y-scroll scroll-container dashboard-scroll">
                            <div class="pr-6 space-y-2">
                            @foreach ($serviceChartData['slices'] as $slice)
                                <div class="flex justify-between items-center gap-3 text-sm md:text-base">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <div class="w-3 h-3 rounded flex-shrink-0" style="background-color: {{ $slice['color'] }}"></div>
                                        <span class="text-slate-700 dark:text-slate-300 truncate">{{ $slice['label'] }}</span>
                                    </div>
                                    <span class="font-semibold text-slate-900 dark:text-white whitespace-nowrap shrink-0 pr-1 pl-2">{{ $slice['value'] }}</span>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No service data available</div>
            @endif
            </div>
            <div data-loading-overlay class="absolute inset-0 hidden items-center justify-center bg-slate-900/35 backdrop-blur-[1px]">
                <div class="flex flex-col items-center justify-center gap-2 rounded-md bg-white/90 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm dark:bg-slate-800/90 dark:text-slate-200">
                    <div class="newtons-cradle" aria-hidden="true">
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                    </div>
                    Please wait...
                </div>
            </div>
        </div>

        <div id="training-modes-card" class="relative rounded-lg glass-card p-5 h-full min-h-[220px] flex flex-col overflow-hidden">
            <div data-card-content class="contents">
            <div class="mb-3 flex items-start justify-between gap-2">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Training Modes</h3>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-1 text-xs" data-dashboard-filter>
                    <input type="hidden" name="facility_year" value="{{ $facilityFilter['year'] }}">
                    <input type="hidden" name="facility_month" value="{{ $facilityFilter['month'] }}">
                    <input type="hidden" name="service_year" value="{{ $serviceFilter['year'] }}">
                    <input type="hidden" name="service_month" value="{{ $serviceFilter['month'] }}">
                    <select name="training_year" class="rounded border-slate-300 bg-white px-2 py-1 text-xs dark:border-slate-600 dark:bg-slate-800">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" @selected((int) $trainingFilter['year'] === (int) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    <select name="training_month" class="rounded border-slate-300 bg-white px-2 py-1 text-xs dark:border-slate-600 dark:bg-slate-800">
                        <option value="">All months</option>
                        @foreach ($months as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((int) $trainingFilter['month'] === (int) $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @if ($trainingModes->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-start flex-1 min-h-0">
                    <div class="relative w-28 h-28 sm:w-24 sm:h-24 md:w-28 md:h-28 max-w-full flex-shrink-0">
                        <canvas id="trainingModesChart" data-slices='@json($trainingModeChartData['slices'])'></canvas>
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <div class="h-36 overflow-hidden scroll-container dashboard-scroll">
                            <div class="pr-6 space-y-2">
                            @foreach ($trainingModeChartData['slices'] as $slice)
                                <div class="flex justify-between items-center gap-3 text-sm md:text-base">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <div class="w-3 h-3 rounded flex-shrink-0" style="background-color: {{ $slice['color'] }}"></div>
                                        <span class="text-slate-700 dark:text-slate-300 truncate">{{ $slice['label'] }}</span>
                                    </div>
                                    <span class="font-semibold text-slate-900 dark:text-white whitespace-nowrap shrink-0 pr-1 pl-2">{{ $slice['value'] }}</span>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-sm text-slate-600 dark:text-slate-400">No training mode data available</div>
            @endif
            </div>
            <div data-loading-overlay class="absolute inset-0 hidden items-center justify-center bg-slate-900/35 backdrop-blur-[1px]">
                <div class="flex flex-col items-center justify-center gap-2 rounded-md bg-white/90 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm dark:bg-slate-800/90 dark:text-slate-200">
                    <div class="newtons-cradle" aria-hidden="true">
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                    </div>
                    Please wait...
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.dashboardRealtimeConfig = {
            token: @json($realtimeToken),
            wsUrl: @json(env('WEBSOCKET_PUBLIC_URL', '')),
        };
    </script>
    @vite('resources/js/admin-dashboard.js')
    <style>
        .scroll-container {
            scrollbar-gutter: stable;
        }

        .dashboard-scroll {
            scrollbar-width: thin;
            scrollbar-color: #ffffff rgba(15, 23, 42, 0.55);
        }

        .dashboard-scroll::-webkit-scrollbar {
            width: 12px;
        }

        .dashboard-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.55);
            border-radius: 9999px;
        }

        .dashboard-scroll::-webkit-scrollbar-thumb {
            background: #ffffff;
            border: 2px solid rgba(15, 23, 42, 0.55);
            border-radius: 9999px;
        }

        .newtons-cradle {
            --uib-size: 40px;
            --uib-speed: 1.2s;
            --uib-color: currentColor;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: var(--uib-size);
            height: var(--uib-size);
        }

        .newtons-cradle__dot {
            position: relative;
            display: flex;
            align-items: center;
            height: 100%;
            width: 25%;
            transform-origin: center top;
        }

        .newtons-cradle__dot::after {
            content: '';
            display: block;
            width: 100%;
            height: 25%;
            border-radius: 50%;
            background-color: var(--uib-color);
        }

        .newtons-cradle__dot:first-child {
            animation: swing var(--uib-speed) linear infinite;
        }

        .newtons-cradle__dot:last-child {
            animation: swing2 var(--uib-speed) linear infinite;
        }

        @keyframes swing {
            0% {
                transform: rotate(0deg);
                animation-timing-function: ease-out;
            }

            25% {
                transform: rotate(70deg);
                animation-timing-function: ease-in;
            }

            50% {
                transform: rotate(0deg);
                animation-timing-function: linear;
            }
        }

        @keyframes swing2 {
            0% {
                transform: rotate(0deg);
                animation-timing-function: linear;
            }

            50% {
                transform: rotate(0deg);
                animation-timing-function: ease-out;
            }

            75% {
                transform: rotate(-70deg);
                animation-timing-function: ease-in;
            }
        }
    </style>
    <script>
        const renderDonutChart = (canvasId, slices = null) => {
            const canvas = document.getElementById(canvasId);
            if (!canvas || !window.Chart) {
                return;
            }

            const parsedSlices = slices ?? JSON.parse(canvas.dataset.slices || '[]');

            const existingChart = window.Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }

            const labels = parsedSlices.map((slice) => slice.label);
            const values = parsedSlices.map((slice) => Number(slice.value));
            const colors = parsedSlices.map((slice) => slice.color);

            new window.Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
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
        };

        const initFilteredDashboardCharts = () => {
            renderDonutChart('topFacilitiesChart');
            renderDonutChart('topServicesChart');
            renderDonutChart('trainingModesChart');
        };

        const setCardLoading = (cardId, isLoading) => {
            const card = document.getElementById(cardId);
            if (!card) {
                return;
            }

            const overlay = card.querySelector('[data-loading-overlay]');
            const content = card.querySelector('[data-card-content]');

            if (!overlay || !content) {
                return;
            }

            if (isLoading) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                content.classList.add('hidden');
            } else {
                overlay.classList.remove('flex');
                overlay.classList.add('hidden');
                content.classList.remove('hidden');
            }
        };

        const initDashboardCharts = () => {
            if (!window.Chart) {
                return false;
            }

            const isDark = document.documentElement.classList.contains('dark');
            const mobileMediaQuery = window.matchMedia('(max-width: 640px)');
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

                let activityChartInstance = null;
                let wasMobile = mobileMediaQuery.matches;

                const renderActivityChart = (isMobile) => {
                    if (activityChartInstance) {
                        activityChartInstance.destroy();
                    }

                    activityChartInstance = new window.Chart(activityCanvas, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                data: totals,
                                borderRadius: 6,
                                backgroundColor: isDark ? '#3b82f6' : '#2563eb',
                                maxBarThickness: isMobile ? 18 : 36,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: isMobile ? 'y' : 'x',
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
                                    beginAtZero: isMobile,
                                    ticks: {
                                        precision: 0,
                                        color: axisColor,
                                    },
                                    grid: {
                                        display: isMobile,
                                        color: isMobile ? gridColor : undefined,
                                    },
                                },
                                y: {
                                    beginAtZero: !isMobile,
                                    ticks: {
                                        precision: 0,
                                        color: axisColor,
                                    },
                                    grid: {
                                        color: gridColor,
                                        display: !isMobile,
                                    },
                                },
                            },
                        },
                    });
                };

                renderActivityChart(wasMobile);

                window.addEventListener('resize', () => {
                    const isMobile = mobileMediaQuery.matches;

                    if (isMobile === wasMobile) {
                        return;
                    }

                    wasMobile = isMobile;
                    renderActivityChart(isMobile);
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
                initFilteredDashboardCharts();
            }

            return true;
        };

        const updateFilteredCards = async (form) => {
            const searchParams = new URLSearchParams(new FormData(form));
            const requestUrl = `${form.action}?${searchParams.toString()}`;
            const activeCard = form.closest('[id$="-card"]');
            const activeCardId = activeCard ? activeCard.id : null;

            const selects = Array.from(form.querySelectorAll('select'));
            selects.forEach((select) => {
                select.disabled = true;
            });
            if (activeCardId) {
                setCardLoading(activeCardId, true);
            }

            try {
                const response = await fetch(requestUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                if (activeCardId) {
                    const currentCard = document.getElementById(activeCardId);
                    const updatedCard = doc.getElementById(activeCardId);

                    if (currentCard && updatedCard) {
                        currentCard.replaceWith(updatedCard);
                    }
                }

                const chartByCard = {
                    'top-facilities-card': 'topFacilitiesChart',
                    'top-services-card': 'topServicesChart',
                    'training-modes-card': 'trainingModesChart',
                };

                const activeCanvasId = activeCardId ? chartByCard[activeCardId] : null;
                if (activeCanvasId) {
                    renderDonutChart(activeCanvasId);
                }

                window.history.replaceState({}, '', requestUrl);
            } finally {
                const targetForm = activeCardId ? document.querySelector(`#${activeCardId} form[data-dashboard-filter]`) : null;
                const updatedSelects = Array.from((targetForm || form).querySelectorAll('select'));
                updatedSelects.forEach((select) => {
                    select.disabled = false;
                });
                if (activeCardId) {
                    setCardLoading(activeCardId, false);
                }
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            if (!initDashboardCharts()) {
                window.addEventListener('dashboard-chart-ready', initDashboardCharts, { once: true });
            }

            document.addEventListener('change', (event) => {
                const select = event.target.closest('form[data-dashboard-filter] select');
                if (!select) {
                    return;
                }

                event.preventDefault();
                updateFilteredCards(select.form);
            });
        });
    </script>
@endpush
