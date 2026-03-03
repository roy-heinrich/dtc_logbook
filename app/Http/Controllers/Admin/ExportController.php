<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        $serviceTypes = Activity::query()
            ->whereNotNull('service_type')
            ->where('service_type', '!=', '')
            ->distinct()
            ->orderBy('service_type')
            ->pluck('service_type');

        // Get preview data based on applied filters
        $query = $this->buildActivitiesQuery($request);
        $activities = (clone $query)
            ->orderByDesc('activity_at')
            ->limit(10)
            ->get();

        $totalUsers = (clone $query)->distinct('user_id')->count('user_id');
        $totalActivities = (clone $query)->count();
        $todayActivities = (clone $query)->whereDate('activity_at', today())->count();
        $filterSummary = $this->getFilterSummary($request);

        return view('admin.reports.index', [
            'serviceTypes' => $serviceTypes,
            'activities' => $activities,
            'totalUsers' => $totalUsers,
            'totalActivities' => $totalActivities,
            'todayActivities' => $todayActivities,
            'filterSummary' => $filterSummary,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $query = $this->buildActivitiesQuery($request);
        $activities = (clone $query)
            ->orderByDesc('activity_at')
            ->get();

        $totalUsers = (clone $query)->distinct('user_id')->count('user_id');
        $totalActivities = (clone $query)->count();
        $todayActivities = (clone $query)->whereDate('activity_at', today())->count();
        $filterSummary = $this->getFilterSummary($request);

        // Create CSV content
        $csv = "DTC LOGBOOK - ACTIVITY REPORT\n";
        $csv .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";

        // Summary Section
        $csv .= "SUMMARY STATISTICS\n";
        $csv .= "Total Users," . $totalUsers . "\n";
        $csv .= "Total Activities," . $totalActivities . "\n";
        $csv .= "Today's Activities," . $todayActivities . "\n\n";

        if (!empty($filterSummary)) {
            $csv .= "FILTERS\n";
            $csv .= implode('; ', $filterSummary) . "\n\n";
        }

        // Activity Logs
        $csv .= "ACTIVITY LOGS\n";
        $csv .= "Name,Email,Facility Used,Service Type,Date,Time\n";
        foreach ($activities as $activity) {
            $activityAt = $activity->activity_at?->timezone(config('app.timezone'));
            $csv .= "\"{$activity->user?->fname_user} {$activity->user?->lname_user}\",";
            $csv .= "\"{$activity->user?->email_user}\",";
            $csv .= "\"{$activity->facility_used}\",";
            $csv .= "\"{$activity->service_type}\",";
            $csv .= ($activityAt?->format('Y-m-d') ?? '-') . ",";
            $csv .= ($activityAt?->format('H:i') ?? '-') . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="dtc-logbook-' . now()->format('Y-m-d-His') . '.csv"');
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildActivitiesQuery($request);
        $activities = (clone $query)
            ->orderByDesc('activity_at')
            ->get();

        $totalUsers = (clone $query)->distinct('user_id')->count('user_id');
        $totalActivities = (clone $query)->count();
        $todayActivities = (clone $query)->whereDate('activity_at', today())->count();
        $filterSummary = $this->getFilterSummary($request);

        $html = '<html>';
        $html .= '<head>';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; margin: 20px; color: #333; }';
        $html .= 'h1, h2 { color: #1f2937; }';
        $html .= 'table { width: 100%; border-collapse: collapse; margin: 15px 0; }';
        $html .= 'table th { background-color: #f3f4f6; padding: 8px; border: 1px solid #d1d5db; text-align: left; font-weight: bold; }';
        $html .= 'table td { padding: 8px; border: 1px solid #d1d5db; }';
        $html .= 'table tr:nth-child(even) { background-color: #f9fafb; }';
        $html .= '.header { margin-bottom: 30px; }';
        $html .= '.stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0; }';
        $html .= '.stat-box { border: 1px solid #d1d5db; padding: 15px; border-radius: 5px; text-align: center; background-color: #f9fafb; }';
        $html .= '.stat-value { font-size: 24px; font-weight: bold; color: #1f2937; }';
        $html .= '.stat-label { font-size: 12px; color: #6b7280; margin-top: 5px; }';
        $html .= '.footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #d1d5db; font-size: 12px; color: #6b7280; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';

        // Header
        $html .= '<div class="header">';
        $html .= '<h1>DTC Logbook - Activity Report</h1>';
        $html .= '<p>Generated on ' . now()->format('Y-m-d H:i:s') . '</p>';
        if (!empty($filterSummary)) {
            $filtersText = htmlspecialchars(implode('; ', $filterSummary), ENT_QUOTES, 'UTF-8');
            $html .= '<p><strong>Filters:</strong> ' . $filtersText . '</p>';
        }
        $html .= '</div>';

        // Summary Stats
        $html .= '<h2>Summary Statistics</h2>';
        $html .= '<div class="stats">';
        $html .= '<div class="stat-box">';
        $html .= '<div class="stat-value">' . $totalUsers . '</div>';
        $html .= '<div class="stat-label">Total Users</div>';
        $html .= '</div>';
        $html .= '<div class="stat-box">';
        $html .= '<div class="stat-value">' . $totalActivities . '</div>';
        $html .= '<div class="stat-label">Total Activities</div>';
        $html .= '</div>';
        $html .= '<div class="stat-box">';
        $html .= '<div class="stat-value">' . $todayActivities . '</div>';
        $html .= '<div class="stat-label">Today\'s Activities</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Activity Logs Table
        $html .= '<h2>User Activity Logs</h2>';
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Name</th>';
        $html .= '<th>Email</th>';
        $html .= '<th>Facility Used</th>';
        $html .= '<th>Service Type</th>';
        $html .= '<th>Date</th>';
        $html .= '<th>Time</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($activities as $activity) {
            $activityAt = $activity->activity_at?->timezone(config('app.timezone'));
            $html .= '<tr>';
            $html .= '<td>' . ($activity->user?->fname_user . ' ' . $activity->user?->lname_user) . '</td>';
            $html .= '<td>' . ($activity->user?->email_user ?? 'N/A') . '</td>';
            $html .= '<td>' . ($activity->facility_used ?? '-') . '</td>';
            $html .= '<td>' . ($activity->service_type ?? '-') . '</td>';
            $html .= '<td>' . ($activityAt?->format('Y-m-d') ?? '-') . '</td>';
            $html .= '<td>' . ($activityAt?->format('H:i') ?? '-') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        // Footer
        $html .= '<div class="footer">';
        $html .= '<p>This is an automated report generated by DTC Logbook System.</p>';
        $html .= '</div>';

        $html .= '</body>';
        $html .= '</html>';

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('dtc-logbook-' . now()->format('Y-m-d-His') . '.pdf');
    }

    public function preview(Request $request)
    {
        $query = $this->buildActivitiesQuery($request);
        $activities = (clone $query)
            ->orderByDesc('activity_at')
            ->limit(10)
            ->get();

        $totalUsers = (clone $query)->distinct('user_id')->count('user_id');
        $totalActivities = (clone $query)->count();
        $todayActivities = (clone $query)->whereDate('activity_at', today())->count();
        $filterSummary = $this->getFilterSummary($request);

        // Build HTML preview
        $html = '';

        if (count($activities) > 0 || $totalActivities > 0) {
            $html .= '<div class="rounded-2xl glass-card shadow-sm max-w-full overflow-hidden">';
            $html .= '<div class="border-b border-slate-200/50 px-6 py-4 dark:border-slate-700/50">';
            $html .= '<h2 class="text-lg font-semibold text-slate-900 dark:text-white">Preview</h2>';
            $html .= '</div>';
            $html .= '<div class="p-6 space-y-6 max-w-full">';

            // Filter Summary
            if (!empty($filterSummary)) {
                $html .= '<div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950">';
                $html .= '<p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Applied Filters</p>';
                $html .= '<div class="space-y-1">';
                foreach ($filterSummary as $filter) {
                    $html .= '<p class="text-sm text-slate-700 dark:text-slate-300">• ' . htmlspecialchars($filter, ENT_QUOTES, 'UTF-8') . '</p>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }

            // Statistics Cards
            $html .= '<div class="grid gap-4 md:grid-cols-3">';
            $html .= '<div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">';
            $html .= '<div class="text-2xl font-bold text-slate-900 dark:text-white">' . $totalUsers . '</div>';
            $html .= '<p class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">Total Users</p>';
            $html .= '</div>';
            $html .= '<div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">';
            $html .= '<div class="text-2xl font-bold text-slate-900 dark:text-white">' . $totalActivities . '</div>';
            $html .= '<p class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">Total Activities</p>';
            $html .= '</div>';
            $html .= '<div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">';
            $html .= '<div class="text-2xl font-bold text-slate-900 dark:text-white">' . $todayActivities . '</div>';
            $html .= '<p class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-1">Today\'s Activities</p>';
            $html .= '</div>';
            $html .= '</div>';

            // Activities Table
            $html .= '<div class="overflow-x-auto w-full">';
            $html .= '<table class="w-full text-xs md:text-sm table-fixed">';
            $html .= '<thead>';
            $html .= '<tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950">';
            $html .= '<th class="px-2 py-2 text-left font-semibold text-slate-900 dark:text-white text-xs whitespace-normal break-words">Name</th>';
            $html .= '<th class="px-2 py-2 text-left font-semibold text-slate-900 dark:text-white text-xs whitespace-normal break-words">Email</th>';
            $html .= '<th class="px-2 py-2 text-left font-semibold text-slate-900 dark:text-white text-xs whitespace-normal break-words">Facility</th>';
            $html .= '<th class="px-2 py-2 text-left font-semibold text-slate-900 dark:text-white text-xs whitespace-normal break-words">Service</th>';
            $html .= '<th class="px-2 py-2 text-left font-semibold text-slate-900 dark:text-white text-xs whitespace-normal break-words">Date</th>';
            $html .= '<th class="px-2 py-2 text-left font-semibold text-slate-900 dark:text-white text-xs whitespace-normal break-words">Time</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody class="divide-y divide-slate-200 dark:divide-slate-800">';

            if (count($activities) > 0) {
                foreach ($activities as $activity) {
                    $activityAt = $activity->activity_at?->timezone(config('app.timezone'));
                    $html .= '<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">';
                    $html .= '<td class="px-2 py-2 text-slate-900 dark:text-slate-100 text-xs whitespace-normal break-words">' . htmlspecialchars($activity->user?->fname_user . ' ' . $activity->user?->lname_user, ENT_QUOTES, 'UTF-8') . '</td>';
                    $html .= '<td class="px-2 py-2 text-slate-900 dark:text-slate-100 text-xs whitespace-normal break-words">' . htmlspecialchars($activity->user?->email_user ?? 'N/A', ENT_QUOTES, 'UTF-8') . '</td>';
                    $html .= '<td class="px-2 py-2 text-slate-900 dark:text-slate-100 text-xs whitespace-normal break-words">' . htmlspecialchars($activity->facility_used ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
                    $html .= '<td class="px-2 py-2 text-slate-900 dark:text-slate-100 text-xs whitespace-normal break-words">' . htmlspecialchars($activity->service_type ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
                    $html .= '<td class="px-2 py-2 text-slate-900 dark:text-slate-100 text-xs whitespace-normal break-words">' . ($activityAt?->format('Y-m-d') ?? '-') . '</td>';
                    $html .= '<td class="px-2 py-2 text-slate-900 dark:text-slate-100 text-xs whitespace-normal break-words">' . htmlspecialchars($activityAt?->format('H:i') ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="6" class="px-2 py-8 text-center text-slate-500 dark:text-slate-400 text-xs">';
                $html .= 'No activities found matching the applied filters.';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';

            // Warning about limited records
            if ($totalActivities > count($activities)) {
                $html .= '<div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-700/60 dark:bg-amber-900/30 dark:text-amber-100">';
                $html .= 'Showing ' . count($activities) . ' of ' . $totalActivities . ' activities. Export to see all records.';
                $html .= '</div>';
            }

            $html .= '</div>';
            $html .= '</div>';
        }

        return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    private function buildActivitiesQuery(Request $request)
    {
        $query = Activity::query()->with('user');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('activity_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ]);
        } elseif (!empty($startDate)) {
            $query->whereDate('activity_at', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('activity_at', '<=', $endDate);
        }

        $serviceType = $request->input('service_type');
        if (!empty($serviceType) && $serviceType !== 'all') {
            $query->where('service_type', $serviceType);
        }

        return $query;
    }

    private function getFilterSummary(Request $request): array
    {
        $summary = [];
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $serviceType = $request->input('service_type');

        if (!empty($startDate)) {
            $summary[] = 'From: ' . $startDate;
        }
        if (!empty($endDate)) {
            $summary[] = 'To: ' . $endDate;
        }
        if (!empty($serviceType) && $serviceType !== 'all') {
            $summary[] = 'Service Type: ' . $serviceType;
        }

        return $summary;
    }
}
