<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\RegUser;
use Illuminate\Http\Request;

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
            ->when($from, fn ($query) => $query->whereDate('activity_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('activity_at', '<=', $to))
            ->orderByDesc('activity_at')
            ->paginate(10)
            ->withQueryString();

        $users = RegUser::orderBy('lname_user')->get(['user_id', 'fname_user', 'lname_user']);

        // Get distinct facilities and services from activities
        $facilities = Activity::whereNotNull('facility_used')
            ->distinct('facility_used')
            ->pluck('facility_used')
            ->sort()
            ->values();

        $serviceTypes = Activity::whereNotNull('service_type')
            ->distinct('service_type')
            ->pluck('service_type')
            ->sort()
            ->values();

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
