<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\RegUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardWidgetController extends Controller
{
    public function stat(Request $request): JsonResponse
    {
        $type = (string) $request->query('type', 'total_users');
        $cacheKey = "dashboard-widget.stat.{$type}";

        $data = Cache::remember($cacheKey, 60, function () use ($type) {
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();

            return match ($type) {
                'total_activities' => [
                    'label' => 'Total Activities',
                    'value' => number_format(Activity::count()),
                ],
                'today_activities' => [
                    'label' => 'Today',
                    'value' => number_format(Activity::whereBetween('activity_at', [$todayStart, $todayEnd])->count()),
                ],
                'latest_activity' => [
                    'label' => 'Latest Activity',
                    'value' => $this->latestActivityLabel(),
                ],
                default => [
                    'label' => 'Total Users',
                    'value' => number_format(RegUser::count()),
                ],
            };
        });

        return response()->json($data);
    }

    public function recentActivities(): JsonResponse
    {
        $rows = Cache::remember('dashboard-widget.recent_activities', 30, function () {
            return Activity::with(['user:user_id,fname_user,lname_user'])
                ->latest('activity_id')
                ->limit(10)
                ->get()
                ->map(function (Activity $activity) {
                    $activityAt = $activity->activity_at?->timezone(config('app.timezone'));

                    return [
                        'id' => $activity->activity_id,
                        'name' => trim(($activity->user?->fname_user ?? '') . ' ' . ($activity->user?->lname_user ?? '')),
                        'facility' => (string) ($activity->facility_used ?? '-'),
                        'service' => (string) ($activity->service_type ?? '-'),
                        'date' => $activityAt?->format('Y-m-d') ?? '-',
                        'time' => $activityAt?->format('H:i') ?? '-',
                    ];
                })
                ->values()
                ->all();
        });

        return response()->json([
            'columns' => [
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'facility', 'label' => 'Facility'],
                ['key' => 'service', 'label' => 'Service'],
                ['key' => 'date', 'label' => 'Date'],
                ['key' => 'time', 'label' => 'Time'],
            ],
            'rows' => $rows,
        ]);
    }

    public function chart(): JsonResponse
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        $todayKey = Carbon::today()->format('Y-m-d');

        $data = Cache::remember("dashboard-widget.chart.{$todayKey}", 60, function () use ($todayStart, $todayEnd) {
            $days = collect(range(6, 0))->map(function (int $offset) {
                $date = Carbon::today()->subDays($offset);

                return [
                    'key' => $date->format('Y-m-d'),
                    'label' => $date->format('D'),
                ];
            });

            $totals = Activity::query()
                ->whereBetween('activity_at', [$todayStart->copy()->subDays(6), $todayEnd])
                ->selectRaw("TO_CHAR(activity_at::date, 'YYYY-MM-DD') as day")
                ->selectRaw('COUNT(*) as total')
                ->groupBy('day')
                ->pluck('total', 'day');

            $labels = [];
            $values = [];

            foreach ($days as $day) {
                $labels[] = $day['label'];
                $values[] = (int) ($totals[$day['key']] ?? 0);
            }

            return [
                'label' => 'Activities',
                'labels' => $labels,
                'values' => $values,
                'color' => '#3b82f6',
            ];
        });

        return response()->json($data);
    }

    private function latestActivityLabel(): string
    {
        $latest = Activity::with(['user:user_id,fname_user,lname_user'])
            ->latest('activity_id')
            ->first();

        if (!$latest) {
            return 'No activity';
        }

        return trim(($latest->user?->fname_user ?? '') . ' ' . ($latest->user?->lname_user ?? '')) ?: 'Latest activity';
    }
}
