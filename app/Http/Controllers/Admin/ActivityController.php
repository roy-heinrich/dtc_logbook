<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\RegUser;
use Illuminate\Http\Request;

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
            ->when($from, fn ($query) => $query->whereDate('activity_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('activity_date', '<=', $to))
            ->orderByDesc('activity_date')
            ->orderByDesc('activity_time')
            ->paginate(20)
            ->withQueryString();

        $users = RegUser::orderBy('lname_user')
            ->get(['user_id', 'fname_user', 'lname_user', 'mname_user']);

        $facilities = Activity::query()
            ->whereNotNull('facility_used')
            ->distinct()
            ->orderBy('facility_used')
            ->pluck('facility_used');

        $serviceTypes = Activity::query()
            ->whereNotNull('service_type')
            ->distinct()
            ->orderBy('service_type')
            ->pluck('service_type');

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
        $users = RegUser::orderBy('lname_user')
            ->get(['user_id', 'fname_user', 'lname_user', 'mname_user']);

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

        Activity::create($data);

        return redirect()
            ->route('admin.activities.index')
            ->with('status', 'Activity created successfully.');
    }

    public function edit(Activity $activity)
    {
        $activity->load('user');

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

        $activity->update($data);

        return redirect()
            ->route('admin.activities.edit', $activity)
            ->with('status', 'Activity updated successfully.');
    }
}
