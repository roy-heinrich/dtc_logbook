<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\RegUser;
use App\Support\CacheVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->integer('user_id') ?: null;
        $facility = $request->string('facility_used')->toString();
        $serviceType = $request->string('service_type')->toString();
        $from = $request->date('from');
        $to = $request->date('to');

        // Query activities directly instead of login logs
        $logs = Activity::with('user')
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->when($facility !== '', fn ($query) => $query->where('facility_used', $facility))
            ->when($serviceType !== '', fn ($query) => $query->where('service_type', $serviceType))
            ->when($from, fn ($query) => $query->where('activity_at', '>=', $from->copy()->startOfDay()))
            ->when($to, fn ($query) => $query->where('activity_at', '<=', $to->copy()->endOfDay()))
            ->orderByDesc('activity_at')
            ->paginate(10)
            ->withQueryString();

        $users = Cache::remember(CacheVersion::key('login_logs_filters', 'users'), 300, function () {
            return RegUser::orderBy('lname_user')->get(['user_id', 'fname_user', 'lname_user']);
        });

        // Get distinct facilities and services from activities
        $facilities = Cache::remember(CacheVersion::key('login_logs_filters', 'facilities'), 300, function () {
            return Activity::whereNotNull('facility_used')
                ->distinct('facility_used')
                ->pluck('facility_used')
                ->sort()
                ->values();
        });

        $serviceTypes = Cache::remember(CacheVersion::key('login_logs_filters', 'service_types'), 300, function () {
            return Activity::whereNotNull('service_type')
                ->distinct('service_type')
                ->pluck('service_type')
                ->sort()
                ->values();
        });

        return view('admin.login-logs.index', [
            'logs' => $logs,
            'users' => $users,
            'facilities' => $facilities,
            'serviceTypes' => $serviceTypes,
            'filters' => [
                'user_id' => $userId,
                'facility_used' => $facility,
                'service_type' => $serviceType,
                'from' => $from?->format('Y-m-d'),
                'to' => $to?->format('Y-m-d'),
            ],
        ]);
    }
}
