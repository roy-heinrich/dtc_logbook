@extends('admin.layouts.app')

@php($pageTitle = 'Widget Dashboard Demo')

@section('content')
    <div class="space-y-6" x-data>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Widget Dashboard Demo</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Each widget loads its own data independently.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <x-stat-widget type="total_users" />
            <x-stat-widget type="total_activities" />
            <x-stat-widget type="today_activities" />
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-chart-widget endpoint="/api/dashboard/chart" canvas-id="dashboardActivityChart" title="Activity Volume (Last 7 days)" />
            <x-table-widget endpoint="/api/dashboard/recent-activities" title="Recent Activities" />
        </div>
    </div>
@endsection
