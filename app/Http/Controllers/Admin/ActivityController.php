<?php

namespace App\Http\Controllers\Admin;

use App\Events\ActivityAdded;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\RegUser;
use App\Support\CacheVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->integer('user_id') ?: null;
        $facility = $request->string('facility_used')->toString();
        $serviceType = $request->string('service_type')->toString();
        $from = $request->date('from');
        $to = $request->date('to');

        $activities = Activity::with('user')
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->when($facility !== '', fn ($query) => $query->where('facility_used', $facility))
            ->when($serviceType !== '', fn ($query) => $query->where('service_type', $serviceType))
            ->when($from, fn ($query) => $query->where('activity_at', '>=', $from->copy()->startOfDay()))
            ->when($to, fn ($query) => $query->where('activity_at', '<=', $to->copy()->endOfDay()))
            ->orderByDesc('activity_at')
            ->paginate(20)
            ->withQueryString();

        $users = Cache::remember(CacheVersion::key('activities_filters', 'users'), 300, function () {
            return RegUser::orderBy('lname_user')
                ->get(['user_id', 'fname_user', 'lname_user', 'mname_user']);
        });

        $facilities = Cache::remember(CacheVersion::key('activities_filters', 'facilities'), 300, function () {
            return Activity::query()
                ->whereNotNull('facility_used')
                ->distinct()
                ->orderBy('facility_used')
                ->pluck('facility_used');
        });

        $serviceTypes = Cache::remember(CacheVersion::key('activities_filters', 'service_types'), 300, function () {
            return Activity::query()
                ->whereNotNull('service_type')
                ->distinct()
                ->orderBy('service_type')
                ->pluck('service_type');
        });

        return view('admin.activities.index', [
            'activities' => $activities,
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

    public function create()
    {
        $users = Cache::remember(CacheVersion::key('activities_filters', 'users'), 300, function () {
            return RegUser::orderBy('lname_user')
                ->get(['user_id', 'fname_user', 'lname_user', 'mname_user']);
        });

        return view('admin.activities.create', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:tbl_regusers,user_id'],
            'facility_used' => ['required', 'string', 'max:255'],
            'service_type' => ['required', 'string', 'max:255'],
            'activity_date' => ['nullable', 'date'],
            'activity_time' => ['nullable', 'date_format:H:i'],
        ]);

        // Combine date and time into activity_at
        $activityAt = null;
        if (!empty($data['activity_date'])) {
            $time = $data['activity_time'] ?? '00:00';
            $activityAt = $data['activity_date'] . ' ' . $time;
        }

        $activity = Activity::create([
            'user_id' => $data['user_id'],
            'facility_used' => $data['facility_used'],
            'service_type' => $data['service_type'],
            'activity_at' => $activityAt,
        ]);

        // Load the user relationship for broadcasting
        $activity->load('user');

        // Broadcast the new activity to connected admins without blocking save on transport issues
        try {
            ActivityAdded::dispatch($activity);
        } catch (Throwable $exception) {
            report($exception);
        }

        CacheVersion::bumpMany(['dashboard', 'activities_filters', 'login_logs_filters', 'reports']);

        return redirect()
            ->route('admin.activities.index')
            ->with('status', 'Activity created successfully.');
    }

    public function edit(Activity $activity)
    {
        $activity->load('user:user_id,fname_user,lname_user');

        return view('admin.activities.edit', [
            'activity' => $activity,
        ]);
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'facility_used' => ['required', 'string', 'max:255'],
            'service_type' => ['required', 'string', 'max:255'],
            'activity_date' => ['nullable', 'date'],
            'activity_time' => ['nullable', 'date_format:H:i'],
        ]);

        // Combine date and time into activity_at
        $activityAt = null;
        if (!empty($data['activity_date'])) {
            $time = $data['activity_time'] ?? '00:00';
            $activityAt = $data['activity_date'] . ' ' . $time;
        }

        $activity->update([
            'facility_used' => $data['facility_used'],
            'service_type' => $data['service_type'],
            'activity_at' => $activityAt,
        ]);

        CacheVersion::bumpMany(['dashboard', 'activities_filters', 'login_logs_filters', 'reports']);

        return redirect()
            ->route('admin.activities.edit', $activity)
            ->with('status', 'Activity updated successfully.');
    }
}
