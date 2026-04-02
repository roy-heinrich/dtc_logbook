<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\CacheVersion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()
            ->orderBy('services_name')
            ->paginate(20);

        return view('admin.services.index', [
            'services' => $services,
        ]);
    }

    public function trash()
    {
        $services = Service::onlyTrashed()
            ->orderBy('services_name')
            ->paginate(20);

        return view('admin.services.trash', [
            'services' => $services,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'services_name' => ['required', 'string', 'max:255', Rule::unique('tbl_services', 'services_name')],
        ]);

        Service::create($data);
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Service added successfully.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', [
            'service' => $service,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'services_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_services', 'services_name')->ignore($service->service_id, 'service_id'),
            ],
        ]);

        $service->update($data);
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Service deleted successfully.');
    }

    public function restore(string $serviceId)
    {
        $service = Service::onlyTrashed()->findOrFail($serviceId);
        $service->restore();
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.services.trash')
            ->with('status', 'Service restored successfully.');
    }

    public function forceDelete(string $serviceId)
    {
        $service = Service::onlyTrashed()->findOrFail($serviceId);
        $service->forceDelete();
        CacheVersion::bumpMany(['dashboard', 'reports']);

        return redirect()
            ->route('admin.services.trash')
            ->with('status', 'Service permanently deleted.');
    }
}
