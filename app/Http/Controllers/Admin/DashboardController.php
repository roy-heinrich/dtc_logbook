<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\LoginLog;
use App\Models\RegUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = RegUser::count();
        $totalActivities = Activity::count();
        $todayActivities = Activity::whereDate('activity_date', today())->count();
        $latestActivity = Activity::orderByDesc('activity_date')
            ->orderByDesc('activity_time')
            ->with('user')
            ->first();

        // Recent user attendance/activity logs (not admin login logs)
        $recentActivities = Activity::with('user')
            ->orderByDesc('activity_date')
            ->orderByDesc('activity_time')
            ->limit(10)
            ->get();

        $activityChart = $this->buildActivityChart();
        $topFacilities = $this->topSummary('facility_used');
        $topServices = $this->topSummary('service_type');

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalActivities' => $totalActivities,
            'todayActivities' => $todayActivities,
            'latestActivity' => $latestActivity,
            'recentActivities' => $recentActivities,
            'activityChart' => $activityChart,
            'topFacilities' => $topFacilities,
            'topServices' => $topServices,
        ]);
    }

    private function buildActivityChart(): Collection
    {
        $startDate = now()->subDays(6)->toDateString();
        $totals = Activity::selectRaw('activity_date, COUNT(*) as total')
            ->whereNotNull('activity_date')
            ->where('activity_date', '>=', $startDate)
            ->groupBy('activity_date')
            ->orderBy('activity_date')
            ->pluck('total', 'activity_date');

        $days = collect(range(0, 6))->map(function (int $offset) {
            return now()->subDays(6 - $offset)->toDateString();
        });

        $points = $days->map(function (string $date) use ($totals) {
            return [
                'date' => $date,
                'total' => (int) ($totals[$date] ?? 0),
            ];
        });

        $max = max(1, $points->max('total'));

        return $points->map(function (array $point) use ($max) {
            $point['percent'] = (int) round(($point['total'] / $max) * 100);

            return $point;
        });
    }

    private function topSummary(string $column): Collection
    {
        return Activity::select($column, DB::raw('COUNT(*) as total'))
            ->whereNotNull($column)
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
}
