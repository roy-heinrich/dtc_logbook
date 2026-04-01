@extends('admin.layouts.app')

@php
    $pageTitle = 'Edit Facility';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Facility</h1>
        <a href="{{ route('admin.facilities.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
            Back to Facilities
        </a>
    </div>

    <div class="rounded-xl glass-card p-6 shadow-sm">
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Facility Details</h2>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700/60 dark:bg-red-900/30 dark:text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.facilities.update', $facility) }}" method="POST" class="mt-4 flex flex-col gap-4">
            @csrf
            @method('PUT')
            <div>
                <label for="facility_name" class="text-sm font-medium text-slate-700 dark:text-slate-200">Facility name</label>
                <input
                    type="text"
                    id="facility_name"
                    name="facility_name"
                    value="{{ old('facility_name', $facility->facility_name) }}"
                    class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    required
                />
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">
                    Save Changes
                </button>
                <a href="{{ route('admin.facilities.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-800 dark:text-slate-300 dark:hover:text-slate-100">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
