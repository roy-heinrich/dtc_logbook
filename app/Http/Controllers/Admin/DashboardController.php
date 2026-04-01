<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\RegUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $todayKey = Carbon::today()->format('Y-m-d');

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

        $totalUsers = Cache::remember('dashboard:total_users', 30, fn () => RegUser::count());
        $totalActivities = Cache::remember('dashboard:total_activities', 30, fn () => Activity::count());
        $todayActivities = Cache::remember(
            "dashboard:today_activities:{$todayKey}",
            30,
            fn () => Activity::whereDate('activity_at', today())->count()
        );

        // Mode of training breakdown
        $trainingModeKey = sprintf('dashboard:training_modes:%s:%s', $trainingYear ?? 'all', $trainingMonth ?? 'all');
        $trainingModes = Cache::remember($trainingModeKey, 60, function () use ($trainingYear, $trainingMonth) {
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
        $latestActivity = Cache::remember('dashboard:latest_activity', 15, fn () => Activity::with('user')->latest('activity_at')->first());

        // Activity chart for last 7 days
        $activityChart = Cache::remember("dashboard:activity_chart:{$todayKey}", 30, function () {
            $chart = collect();
            $maxCount = 1;

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $count = Activity::whereDate('activity_at', $date)->count();
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
        $topFacilitiesKey = sprintf('dashboard:top_facilities:%s:%s', $facilityYear ?? 'all', $facilityMonth ?? 'all');
        $topFacilities = Cache::remember($topFacilitiesKey, 60, function () use ($facilityYear, $facilityMonth) {
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
        $topServicesKey = sprintf('dashboard:top_services:%s:%s', $serviceYear ?? 'all', $serviceMonth ?? 'all');
        $topServices = Cache::remember($topServicesKey, 60, function () use ($serviceYear, $serviceMonth) {
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
        $mostActiveUsers = Cache::remember('dashboard:most_active_users', 60, function () {
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
        $genderStats = Cache::remember('dashboard:gender_stats', 60, function () {
            return RegUser::selectRaw('sex_user, COUNT(*) as total')
                ->groupBy('sex_user')
                ->get();
        });

        $genderChartData = $this->preparePieChartData($genderStats, [
            '#3b82f6', // blue-500 for Male
            '#ec4899', // pink-500 for Female
        ], 'sex_user');

        return view('admin.dashboard', [
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
            'facilityFilter' => ['year' => $facilityYear, 'month' => $facilityMonth],
            'serviceFilter' => ['year' => $serviceYear, 'month' => $serviceMonth],
            'trainingFilter' => ['year' => $trainingYear, 'month' => $trainingMonth],
        ]);
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
        if ($year !== null) {
            $query->whereYear('activity_at', $year);
        }

        if ($month !== null) {
            $query->whereMonth('activity_at', $month);
        }
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
            $percent = ($item->total / $total) * 100;
            $color = $colors[$index % count($colors)];
            
            $slices[] = [
                'label' => data_get($item, $field),
                'value' => $item->total,
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
}
