<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\DashboardSnapshot;
use App\Models\RegUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardSnapshotService
{
    public function refresh(): void
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $availableYears = Activity::query()
            ->whereNotNull('activity_at')
            ->selectRaw('EXTRACT(YEAR FROM activity_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->values()
            ->all();

        $totalUsers = RegUser::count();
        $totalActivities = Activity::count();
        $todayActivities = Activity::whereBetween('activity_at', [$todayStart, $todayEnd])->count();
        $latestActivity = Activity::with(['user:user_id,fname_user,lname_user'])->latest('activity_id')->first();

        $activityChart = $this->buildActivityChart();
        $topFacilities = $this->getGroupedCount('facility_used', 6);
        $topServices = $this->getGroupedCount('service_type', 6);
        $trainingModes = $this->getGroupedCount('md_training');
        $mostActiveUsers = RegUser::withCount('activities')
            ->orderByDesc('activities_count')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => trim("{$user->fname_user} {$user->lname_user}"),
                    'count' => $user->activities_count,
                ];
            })
            ->all();

        $genderStats = RegUser::selectRaw('sex_user, COUNT(*) as total')
            ->groupBy('sex_user')
            ->get()
            ->all();

        DashboardSnapshot::updateOrCreate(
            ['snapshot_key' => 'dashboard:default'],
            [
                'payload' => [
                    'available_years' => $availableYears,
                    'total_users' => $totalUsers,
                    'total_activities' => $totalActivities,
                    'today_activities' => $todayActivities,
                    'latest_activity' => $this->serializeLatestActivity($latestActivity),
                    'activity_chart' => $activityChart,
                    'top_facilities' => $topFacilities,
                    'top_services' => $topServices,
                    'training_modes' => $trainingModes,
                    'most_active_users' => $mostActiveUsers,
                    'gender_stats' => $genderStats,
                ],
                'refreshed_at' => now(),
            ]
        );
    }

    private function buildActivityChart(): array
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
        })->all();
    }

    private function getGroupedCount(string $field, int $limit = 0): array
    {
        $query = Activity::query()
            ->whereNotNull($field)
            ->where($field, '!=', '');

        $query = $query
            ->selectRaw("{$field}, COUNT(*) as total")
            ->groupBy($field)
            ->orderByDesc('total');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get()->all();
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
