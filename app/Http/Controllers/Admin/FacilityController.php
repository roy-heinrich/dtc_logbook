<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Support\CacheVersion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::query()
            ->orderBy('facility_name')
            ->paginate(20);

        return view('admin.facilities.index', [
            'facilities' => $facilities,
        ]);
    }

    public function trash()
    {
        $facilities = Facility::onlyTrashed()
            ->orderBy('facility_name')
            ->paginate(20);

        return view('admin.facilities.trash', [
            'facilities' => $facilities,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'facility_name' => ['required', 'string', 'max:255', Rule::unique('tbl_facility', 'facility_name')],
        ]);

        Facility::create($data);
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.facilities.index')
            ->with('status', 'Facility added successfully.');
    }

    public function edit(Facility $facility)
    {
        return view('admin.facilities.edit', [
            'facility' => $facility,
        ]);
    }

    public function update(Request $request, Facility $facility)
    {
        $data = $request->validate([
            'facility_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_facility', 'facility_name')->ignore($facility->facility_id, 'facility_id'),
            ],
        ]);

        $facility->update($data);
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.facilities.index')
            ->with('status', 'Facility updated successfully.');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.facilities.index')
            ->with('status', 'Facility deleted successfully.');
    }

    public function restore(string $facilityId)
    {
        $facility = Facility::onlyTrashed()->findOrFail($facilityId);
        $facility->restore();
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.facilities.trash')
            ->with('status', 'Facility restored successfully.');
    }

    public function forceDelete(string $facilityId)
    {
        $facility = Facility::onlyTrashed()->findOrFail($facilityId);
        $facility->forceDelete();
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.facilities.trash')
            ->with('status', 'Facility permanently deleted.');
    }
}
