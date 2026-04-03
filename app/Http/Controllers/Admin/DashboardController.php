<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\DashboardSnapshot;
use App\Models\RegUser;
use App\Services\DashboardSnapshotService;
use App\Support\CacheVersion;
use App\Support\RealtimeToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        Log::info('dashboard.index.started', [
            'admin_id' => auth('admin')->id(),
            'cache_store' => config('cache.default'),
            'redis_client' => config('database.redis.client'),
            'has_redis_url' => !empty(env('REDIS_URL')),
            'query' => $request->query(),
        ]);

        try {
        $todayKey = Carbon::today()->format('Y-m-d');
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $snapshot = DashboardSnapshot::query()
            ->where('snapshot_key', 'dashboard:default')
            ->first();

        $snapshotMaxAgeSeconds = max(10, (int) env('DASHBOARD_SNAPSHOT_MAX_AGE_SECONDS', 120));
        $isSnapshotFresh = $snapshot !== null
            && $snapshot->refreshed_at !== null
            && $snapshot->refreshed_at->greaterThan(now()->subSeconds($snapshotMaxAgeSeconds));

        $availableYears = $isSnapshotFresh
            ? collect(data_get($snapshot->payload, 'available_years', []))
            : Cache::remember(
                CacheVersion::key('dashboard', 'available_years'),
                3600,
                fn () => Activity::query()
                    ->whereNotNull('activity_at')
                    ->selectRaw('EXTRACT(YEAR FROM activity_at) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year')
                    ->filter()
                    ->values()
            );

        $months = collect(range(1, 12))->mapWithKeys(
            fn ($month) => [$month => Carbon::create()->month($month)->format('F')]
        );

        $currentYear = (int) now()->year;
        $defaultYear = $availableYears->contains($currentYear) ? $currentYear : $availableYears->first();

        $facilityYear = $this->sanitizeYear($request->query('facility_year'), $availableYears, $defaultYear);
        $facilityMonth = $this->sanitizeMonth($request->query('facility_month'));
        $serviceYear = $this->sanitizeYear($request->query('service_year'), $availableYears, $defaultYear);
        $serviceMonth = $this->sanitizeMonth($request->query('service_month'));
        $trainingYear = $this->sanitizeYear($request->query('training_year'), $availableYears, $defaultYear);
        $trainingMonth = $this->sanitizeMonth($request->query('training_month'));

        $usingDefaultFilters =
            $request->query('facility_year') === null &&
            $request->query('facility_month') === null &&
            $request->query('service_year') === null &&
            $request->query('service_month') === null &&
            $request->query('training_year') === null &&
            $request->query('training_month') === null;

        if ($usingDefaultFilters) {
            if ($snapshot) {
                if (!$isSnapshotFresh) {
                    dispatch(function (): void {
                        try {
                            app(DashboardSnapshotService::class)->refresh();
                        } catch (Throwable $exception) {
                            report($exception);
                        }
                    })->afterResponse();
                }

                $liveLatestActivity = Activity::with(['user:user_id,fname_user,lname_user'])
                    ->latest('activity_id')
                    ->first();

                $liveTotalActivities = Activity::count();
                $liveTodayActivities = Activity::whereBetween('activity_at', [$todayStart, $todayEnd])->count();

                $snapshotPayload = $snapshot->payload;
                $snapshotPayload['latest_activity'] = $this->serializeLatestActivity($liveLatestActivity);
                $snapshotPayload['total_activities'] = $liveTotalActivities;
                $snapshotPayload['today_activities'] = $liveTodayActivities;

                $response = view('admin.dashboard', $this->buildDashboardViewDataFromSnapshot(
                    $snapshotPayload,
                    $facilityYear,
                    $facilityMonth,
                    $serviceYear,
                    $serviceMonth,
                    $trainingYear,
                    $trainingMonth,
                    $availableYears,
                    $months
                ));

                Log::info('dashboard.index.completed', [
                    'admin_id' => auth('admin')->id(),
                    'mode' => $isSnapshotFresh ? 'precomputed' : 'precomputed_stale_refreshing',
                ]);

                return $response;
            }
        }

        $totalUsers = Cache::remember(CacheVersion::key('dashboard', 'total_users'), 900, fn () => RegUser::count());
        $totalActivities = Cache::remember(CacheVersion::key('dashboard', 'total_activities'), 900, fn () => Activity::count());
        $todayActivities = Cache::remember(
            CacheVersion::key('dashboard', "today_activities:{$todayKey}"),
            300,
            fn () => Activity::whereBetween('activity_at', [$todayStart, $todayEnd])->count()
        );

        // Mode of training breakdown
        $trainingModeKey = CacheVersion::key('dashboard', sprintf('training_modes:%s:%s', $trainingYear ?? 'all', $trainingMonth ?? 'all'));
        $trainingModes = Cache::remember($trainingModeKey, 900, function () use ($trainingYear, $trainingMonth) {
            $query = Activity::query()
                ->whereNotNull('md_training')
                ->where('md_training', '!=', '');

            $this->applyYearMonthFilter($query, $trainingYear, $trainingMonth);

            return $query
                ->selectRaw('md_training, COUNT(*) as total')
                ->groupBy('md_training')
                ->orderByDesc('total')
                ->get();
        });

        $trainingModeChartData = $this->preparePieChartData($trainingModes, [
            '#10b981', // emerald-600
            '#f59e0b', // amber-600
            '#06b6d4', // cyan-600
            '#8b5cf6', // violet-600
            '#ec4899', // pink-600
            '#14b8a6', // teal-600
        ], 'md_training');
        $latestActivity = Cache::remember(
            CacheVersion::key('dashboard', 'latest_activity'),
            300,
            fn () => Activity::with(['user:user_id,fname_user,lname_user'])->latest('activity_id')->first()
        );

        // Activity chart for last 7 days
        $activityChart = Cache::remember(CacheVersion::key('dashboard', "activity_chart:{$todayKey}"), 300, function () {
            $startDate = Carbon::today()->subDays(6)->startOfDay();
            $endDate = Carbon::today()->endOfDay();

            $dailyCounts = Activity::query()
                ->whereBetween('activity_at', [$startDate, $endDate])
                ->selectRaw('DATE(activity_at) as activity_date, COUNT(*) as total')
                ->groupBy(DB::raw('DATE(activity_at)'))
                ->orderBy('activity_date')
                ->pluck('total', 'activity_date');

            $chart = collect();
            $maxCount = 1;

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $count = (int) ($dailyCounts[$date->toDateString()] ?? 0);
                $maxCount = max($maxCount, $count);

                $chart->push([
                    'date' => $date->format('Y-m-d'),
                    'total' => $count,
                    'percent' => 0,
                ]);
            }

            return $chart->map(function ($point) use ($maxCount) {
                $point['percent'] = $maxCount > 0 ? ($point['total'] / $maxCount) * 100 : 0;
                return $point;
            });
        });

        // Top facilities with pie chart data
        $topFacilitiesKey = CacheVersion::key('dashboard', sprintf('top_facilities:%s:%s', $facilityYear ?? 'all', $facilityMonth ?? 'all'));
        $topFacilities = Cache::remember($topFacilitiesKey, 900, function () use ($facilityYear, $facilityMonth) {
            $query = Activity::query()
                ->whereNotNull('facility_used')
                ->where('facility_used', '!=', '');

            $this->applyYearMonthFilter($query, $facilityYear, $facilityMonth);

            return $query
                ->selectRaw('facility_used, COUNT(*) as total')
                ->groupBy('facility_used')
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        });

        $facilityChartData = $this->preparePieChartData($topFacilities, [
            '#2563eb', // blue-600
            '#dc2626', // red-600
            '#16a34a', // green-600
            '#d97706', // amber-600
            '#7c3aed', // violet-600
            '#0891b2', // cyan-600
        ], 'facility_used');

        // Top services with pie chart data
        $topServicesKey = CacheVersion::key('dashboard', sprintf('top_services:%s:%s', $serviceYear ?? 'all', $serviceMonth ?? 'all'));
        $topServices = Cache::remember($topServicesKey, 900, function () use ($serviceYear, $serviceMonth) {
            $query = Activity::query()
                ->whereNotNull('service_type')
                ->where('service_type', '!=', '');

            $this->applyYearMonthFilter($query, $serviceYear, $serviceMonth);

            return $query
                ->selectRaw('service_type, COUNT(*) as total')
                ->groupBy('service_type')
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        });

        $serviceChartData = $this->preparePieChartData($topServices, [
            '#9333ea', // purple-600
            '#ea580c', // orange-600
            '#0f766e', // teal-700
            '#1d4ed8', // blue-700
            '#be123c', // rose-700
            '#65a30d', // lime-600
        ], 'service_type');

        // Most active users
        $mostActiveUsers = Cache::remember(CacheVersion::key('dashboard', 'most_active_users'), 900, function () {
            return RegUser::withCount('activities')
                ->orderByDesc('activities_count')
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => trim("{$user->fname_user} {$user->lname_user}"),
                        'count' => $user->activities_count,
                    ];
                });
        });

        // Gender ratio
        $genderStats = Cache::remember(CacheVersion::key('dashboard', 'gender_stats'), 900, function () {
            return RegUser::selectRaw('sex_user, COUNT(*) as total')
                ->groupBy('sex_user')
                ->get();
        });

        $genderChartData = $this->preparePieChartData($genderStats, [
            '#3b82f6', // blue-500 for Male
            '#ec4899', // pink-500 for Female
        ], 'sex_user');

        $adminId = (int) (auth('admin')->id() ?? 0);
        $realtimeEnabled = filter_var(env('DASHBOARD_REALTIME_ENABLED', false), FILTER_VALIDATE_BOOL);
        $websocketPublicUrl = trim((string) env('WEBSOCKET_PUBLIC_URL', ''));

        $realtimeToken = ($realtimeEnabled && $websocketPublicUrl !== '' && $adminId > 0)
            ? RealtimeToken::issue($adminId, 'dashboard', (int) env('WEBSOCKET_TOKEN_TTL', 60))
            : null;

        $response = view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalActivities' => $totalActivities,
            'todayActivities' => $todayActivities,
            'latestActivity' => $latestActivity,
            'activityChart' => $activityChart,
            'topFacilities' => $topFacilities,
            'topServices' => $topServices,
            'trainingModes' => $trainingModes,
            'facilityChartData' => $facilityChartData,
            'serviceChartData' => $serviceChartData,
            'trainingModeChartData' => $trainingModeChartData,
            'mostActiveUsers' => $mostActiveUsers,
            'genderStats' => $genderStats,
            'genderChartData' => $genderChartData,
            'availableYears' => $availableYears,
            'months' => $months,
            'realtimeToken' => $realtimeToken,
            'facilityFilter' => ['year' => $facilityYear, 'month' => $facilityMonth],
            'serviceFilter' => ['year' => $serviceYear, 'month' => $serviceMonth],
            'trainingFilter' => ['year' => $trainingYear, 'month' => $trainingMonth],
        ]);

        Log::info('dashboard.index.completed', [
            'admin_id' => auth('admin')->id(),
        ]);

        return $response;
        } catch (Throwable $exception) {
            Log::error('dashboard.index.failed', [
                'admin_id' => auth('admin')->id(),
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            $showActualErrors = filter_var(env('SHOW_ACTUAL_ERRORS', false), FILTER_VALIDATE_BOOL);
            if ($showActualErrors) {
                return response()->make(
                    '<h1>Dashboard Error</h1><pre style="white-space:pre-wrap">' . e($exception->getMessage()) . '</pre>',
                    500,
                    ['Content-Type' => 'text/html; charset=UTF-8']
                );
            }

            throw $exception;
        }
    }

    private function sanitizeYear(mixed $year, $availableYears, ?int $defaultYear): ?int
    {
        if ($year === null || $year === '') {
            return $defaultYear;
        }

        if (!is_numeric($year)) {
            return $defaultYear;
        }

        $value = (int) $year;
        if (!$availableYears->contains($value)) {
            return $defaultYear;
        }

        return $value;
    }

    private function sanitizeMonth(mixed $month): ?int
    {
        if ($month === null || $month === '') {
            return null;
        }

        if (!is_numeric($month)) {
            return null;
        }

        $value = (int) $month;

        return $value >= 1 && $value <= 12 ? $value : null;
    }

    private function applyYearMonthFilter($query, ?int $year, ?int $month): void
    {
        if ($year === null) {
            return;
        }

        $start = $month !== null
            ? Carbon::create($year, $month, 1)->startOfMonth()
            : Carbon::create($year, 1, 1)->startOfYear();

        $end = $month !== null
            ? Carbon::create($year, $month, 1)->endOfMonth()
            : Carbon::create($year, 12, 31)->endOfYear();

        $query->whereBetween('activity_at', [$start, $end]);
    }

    /**
     * Prepare pie chart data with slices and conic-gradient
     */
    private function preparePieChartData($items, $colors, $field)
    {
        if ($items->isEmpty()) {
            return [
                'slices' => [],
                'gradient' => 'conic-gradient(#e2e8f0 0% 100%)',
            ];
        }

        $total = $items->sum('total');
        $slices = [];
        $gradientParts = [];
        $currentPercent = 0;

        foreach ($items as $index => $item) {
            $totalValue = (float) data_get($item, 'total', 0);
            $percent = $totalValue > 0 ? ($totalValue / $total) * 100 : 0;
            $color = $colors[$index % count($colors)];
            
            $slices[] = [
                'label' => data_get($item, $field),
                'value' => $totalValue,
                'percent' => $percent,
                'color' => $color,
            ];

            $startPercent = $currentPercent;
            $currentPercent += $percent;
            $gradientParts[] = "{$color} {$startPercent}% {$currentPercent}%";
        }

        $gradient = 'conic-gradient(' . implode(', ', $gradientParts) . ')';

        return [
            'slices' => $slices,
            'gradient' => $gradient,
        ];
    }

    private function buildDashboardViewDataFromSnapshot(array $payload, ?int $facilityYear, ?int $facilityMonth, ?int $serviceYear, ?int $serviceMonth, ?int $trainingYear, ?int $trainingMonth, $availableYears, $months): array
    {
        $latestActivity = $this->hydrateLatestActivity(data_get($payload, 'latest_activity'));

        $trainingModes = collect(data_get($payload, 'training_modes', []));
        $topFacilities = collect(data_get($payload, 'top_facilities', []));
        $topServices = collect(data_get($payload, 'top_services', []));
        $mostActiveUsers = collect(data_get($payload, 'most_active_users', []));
        $genderStats = collect(data_get($payload, 'gender_stats', []));

        $trainingModeChartData = $this->preparePieChartData($trainingModes, [
            '#10b981',
            '#f59e0b',
            '#06b6d4',
            '#8b5cf6',
            '#ec4899',
            '#14b8a6',
        ], 'md_training');

        $facilityChartData = $this->preparePieChartData($topFacilities, [
            '#2563eb',
            '#dc2626',
            '#16a34a',
            '#d97706',
            '#7c3aed',
            '#0891b2',
        ], 'facility_used');

        $serviceChartData = $this->preparePieChartData($topServices, [
            '#9333ea',
            '#ea580c',
            '#0f766e',
            '#1d4ed8',
            '#be123c',
            '#65a30d',
        ], 'service_type');

        $genderChartData = $this->preparePieChartData($genderStats, [
            '#3b82f6',
            '#ec4899',
        ], 'sex_user');

        $adminId = (int) (auth('admin')->id() ?? 0);
        $realtimeEnabled = filter_var(env('DASHBOARD_REALTIME_ENABLED', false), FILTER_VALIDATE_BOOL);
        $websocketPublicUrl = trim((string) env('WEBSOCKET_PUBLIC_URL', ''));
        $realtimeToken = ($realtimeEnabled && $websocketPublicUrl !== '' && $adminId > 0)
            ? RealtimeToken::issue($adminId, 'dashboard', (int) env('WEBSOCKET_TOKEN_TTL', 60))
            : null;

        return [
            'totalUsers' => (int) data_get($payload, 'total_users', 0),
            'totalActivities' => (int) data_get($payload, 'total_activities', 0),
            'todayActivities' => (int) data_get($payload, 'today_activities', 0),
            'latestActivity' => $latestActivity,
            'activityChart' => collect(data_get($payload, 'activity_chart', [])),
            'topFacilities' => $topFacilities,
            'topServices' => $topServices,
            'trainingModes' => $trainingModes,
            'facilityChartData' => $facilityChartData,
            'serviceChartData' => $serviceChartData,
            'trainingModeChartData' => $trainingModeChartData,
            'mostActiveUsers' => $mostActiveUsers,
            'genderStats' => $genderStats,
            'genderChartData' => $genderChartData,
            'availableYears' => collect(data_get($payload, 'available_years', [])),
            'months' => $months,
            'realtimeToken' => $realtimeToken,
            'facilityFilter' => ['year' => $facilityYear, 'month' => $facilityMonth],
            'serviceFilter' => ['year' => $serviceYear, 'month' => $serviceMonth],
            'trainingFilter' => ['year' => $trainingYear, 'month' => $trainingMonth],
        ];
    }

    private function hydrateLatestActivity(?array $payload): ?object
    {
        if ($payload === null) {
            return null;
        }

        $latestActivity = (object) [
            'activity_id' => $payload['activity_id'] ?? null,
            'activity_at' => !empty($payload['activity_at']) ? Carbon::parse($payload['activity_at']) : null,
        ];

        $latestActivity->user = !empty($payload['user']) ? (object) $payload['user'] : null;

        return $latestActivity;
    }

    private function serializeLatestActivity(?Activity $activity): ?array
    {
        if (!$activity) {
            return null;
        }

        return [
            'activity_id' => $activity->activity_id,
            'activity_at' => $activity->activity_at?->toIso8601String(),
            'user' => $activity->user ? [
                'fname_user' => $activity->user->fname_user,
                'lname_user' => $activity->user->lname_user,
            ] : null,
        ];
    }
}
