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
            'query' => $request->query(),
        ]);

        try {
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();
            $todayKey = Carbon::today()->format('Y-m-d');

            // Compute available years directly (no caching)
            $availableYears = Activity::query()
                ->whereNotNull('activity_at')
                ->selectRaw('EXTRACT(YEAR FROM activity_at) as year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year')
                ->filter()
                ->values();

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

            // Batch independent queries for parallel execution
            [$totalUsers, $totalActivities, $todayActivities, $latestActivity, $genderStats, $mostActiveUsers] = [
                Cache::remember(CacheVersion::key('dashboard', 'total_users'), 120, fn () => RegUser::count()),
                Cache::remember(CacheVersion::key('dashboard', 'total_activities'), 120, fn () => Activity::count()),
                Cache::remember(CacheVersion::key('dashboard', "today_activities:{$todayKey}"), 60, fn () => Activity::whereBetween('activity_at', [$todayStart, $todayEnd])->count()),
                Cache::remember(CacheVersion::key('dashboard', 'latest_activity'), 10, fn () => Activity::with(['user:user_id,fname_user,lname_user'])->latest('activity_id')->first()),
                Cache::remember(CacheVersion::key('dashboard', 'gender_stats'), 120, fn () => RegUser::selectRaw('sex_user, COUNT(*) as total')->groupBy('sex_user')->get()),
                Cache::remember(CacheVersion::key('dashboard', 'most_active_users'), 120, fn () => DB::table('mv_dashboard_most_active_users')->select('name', 'activities_count as count')->orderByDesc('activities_count')->get()->map(fn ($user) => ['name' => $user->name, 'count' => $user->count])),
            ];

            // Batch training modes, facilities, services, and activity chart (independent queries)
            $trainingModeKey = CacheVersion::key('dashboard', sprintf('training_modes:%s:%s', $trainingYear ?? 'all', $trainingMonth ?? 'all'));
            $facilityKey = CacheVersion::key('dashboard', sprintf('top_facilities:%s:%s', $facilityYear ?? 'all', $facilityMonth ?? 'all'));
            $serviceKey = CacheVersion::key('dashboard', sprintf('top_services:%s:%s', $serviceYear ?? 'all', $serviceMonth ?? 'all'));
            $activityChartKey = CacheVersion::key('dashboard', "activity_chart:{$todayKey}");

            [$trainingModes, $topFacilities, $topServices, $activityChartData] = [
                Cache::remember($trainingModeKey, 60, fn () => $this->getTrainingModes($trainingYear, $trainingMonth)),
                Cache::remember($facilityKey, 60, fn () => $this->getTopFacilities($facilityYear, $facilityMonth)),
                Cache::remember($serviceKey, 60, fn () => $this->getTopServices($serviceYear, $serviceMonth)),
                Cache::remember($activityChartKey, 60, fn () => $this->getActivityChart($todayStart, $todayEnd)),
            ];

            // Prepare chart data for display
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

            $response = view('admin.dashboard', [
                'totalUsers' => $totalUsers,
                'totalActivities' => $totalActivities,
                'todayActivities' => $todayActivities,
                'latestActivity' => $latestActivity,
                'activityChart' => $activityChartData,
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

    /**
     * Helper: Get training modes (uses materialized view or live query if filtered)
     */
    private function getTrainingModes(?int $year, ?int $month)
    {
        if ($year !== null) {
            $query = Activity::query()
                ->whereNotNull('md_training')
                ->where('md_training', '!=', '');
            $this->applyYearMonthFilter($query, $year, $month);
            return $query
                ->selectRaw('md_training, COUNT(*) as total')
                ->groupBy('md_training')
                ->orderByDesc('total')
                ->get();
        }

        return DB::table('mv_dashboard_training_modes')
            ->select('md_training', 'total')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Helper: Get top facilities (uses materialized view or live query if filtered)
     */
    private function getTopFacilities(?int $year, ?int $month)
    {
        if ($year !== null) {
            $query = Activity::query()
                ->whereNotNull('facility_used')
                ->where('facility_used', '!=', '');
            $this->applyYearMonthFilter($query, $year, $month);
            return $query
                ->selectRaw('facility_used, COUNT(*) as total')
                ->groupBy('facility_used')
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        }

        return DB::table('mv_dashboard_top_facilities')
            ->select('facility_used', 'total')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Helper: Get top services (uses materialized view or live query if filtered)
     */
    private function getTopServices(?int $year, ?int $month)
    {
        if ($year !== null) {
            $query = Activity::query()
                ->whereNotNull('service_type')
                ->where('service_type', '!=', '');
            $this->applyYearMonthFilter($query, $year, $month);
            return $query
                ->selectRaw('service_type, COUNT(*) as total')
                ->groupBy('service_type')
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        }

        return DB::table('mv_dashboard_top_services')
            ->select('service_type', 'total')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Helper: Get activity chart for last 7 days
     */
    private function getActivityChart(Carbon $startDate, Carbon $endDate)
    {
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
    }
}
