@extends('admin.layouts.app')

@php($pageTitle = 'Reports')

@section('content')
<div class="space-y-6 overflow-x-hidden max-w-full">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reports</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Filter activity data before exporting.</p>
        </div>
    </div>

    <div class="rounded-2xl glass-card p-6 shadow-sm">
        <div class="space-y-6">
            <div class="grid gap-6 md:grid-cols-3">
                <div class="space-y-2">
                    <label for="start_date" class="text-xs font-semibold uppercase tracking-widest text-slate-400">From</label>
                    <div x-data="datePicker('start_date', '{{ request('start_date') }}')" class="relative">
                        <input type="hidden" name="start_date" :value="value" />
                        <div class="relative">
                            <input
                                type="text"
                                id="start_date"
                                x-ref="input"
                                x-model="displayMasked"
                                @click="open = true"
                                @keydown.escape.prevent="open = false"
                                placeholder="YYYY-MM-DD"
                                readonly
                                class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 pr-10 text-sm text-slate-500 shadow-sm cursor-not-allowed dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400"
                            />
                            <button type="button" @click="open = true" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                        <div
                            x-show="open"
                            x-transition
                            @click.outside="open = false"
                            class="absolute z-20 mt-2 w-[min(18rem,calc(100vw-2rem))] left-0 right-0 mx-auto rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-slate-800 dark:bg-slate-900"
                        >
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <button type="button" @click="prevMonth" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800">&larr;</button>
                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="monthLabel"></div>
                                    <button type="button" @click="nextMonth" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800">&rarr;</button>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" @click="prevYear" class="flex-1 rounded-lg bg-slate-100 p-1 text-xs font-medium text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700">&larr; Year</button>
                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="year"></div>
                                    <button type="button" @click="nextYear" class="flex-1 rounded-lg bg-slate-100 p-1 text-xs font-medium text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700">Year &rarr;</button>
                                </div>
                            </div>
                            <div class="mt-3 grid grid-cols-7 gap-1 text-center text-xs text-slate-400">
                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            </div>
                            <div class="mt-2 grid grid-cols-7 gap-1">
                                <template x-for="day in days" :key="day.key">
                                    <button
                                        type="button"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-xs"
                                        :class="day.isEmpty ? 'text-transparent' : (isSelected(day.date) ? 'bg-brand-500 text-white' : (isToday(day.date) ? 'bg-brand-100 text-brand-800 dark:bg-brand-900/40 dark:text-brand-100' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800'))"
                                        :disabled="day.isEmpty"
                                        @click="selectDate(day.date)"
                                        x-text="day.label"
                                    ></button>
                                </template>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-xs">
                                <button type="button" @click="clearDate" class="text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white">Clear</button>
                                <button type="button" @click="selectToday" class="text-brand-600 hover:text-brand-700">Today</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="end_date" class="text-xs font-semibold uppercase tracking-widest text-slate-400">To</label>
                    <div x-data="datePicker('end_date', '{{ request('end_date') }}')" class="relative">
                        <input type="hidden" name="end_date" :value="value" />
                        <div class="relative">
                            <input
                                type="text"
                                id="end_date"
                                x-ref="input"
                                x-model="displayMasked"
                                @click="open = true"
                                @keydown.escape.prevent="open = false"
                                placeholder="YYYY-MM-DD"
                                readonly
                                class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 pr-10 text-sm text-slate-500 shadow-sm cursor-not-allowed dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400"
                            />
                            <button type="button" @click="open = true" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                        <div
                            x-show="open"
                            x-transition
                            @click.outside="open = false"
                            class="absolute z-20 mt-2 w-[min(18rem,calc(100vw-2rem))] left-0 right-0 mx-auto rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-slate-800 dark:bg-slate-900"
                        >
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <button type="button" @click="prevMonth" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800">&larr;</button>
                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="monthLabel"></div>
                                    <button type="button" @click="nextMonth" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800">&rarr;</button>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" @click="prevYear" class="flex-1 rounded-lg bg-slate-100 p-1 text-xs font-medium text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700">&larr; Year</button>
                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="year"></div>
                                    <button type="button" @click="nextYear" class="flex-1 rounded-lg bg-slate-100 p-1 text-xs font-medium text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700">Year &rarr;</button>
                                </div>
                            </div>
                            <div class="mt-3 grid grid-cols-7 gap-1 text-center text-xs text-slate-400">
                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            </div>
                            <div class="mt-2 grid grid-cols-7 gap-1">
                                <template x-for="day in days" :key="day.key">
                                    <button
                                        type="button"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-xs"
                                        :class="day.isEmpty ? 'text-transparent' : (isSelected(day.date) ? 'bg-brand-500 text-white' : (isToday(day.date) ? 'bg-brand-100 text-brand-800 dark:bg-brand-900/40 dark:text-brand-100' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800'))"
                                        :disabled="day.isEmpty"
                                        @click="selectDate(day.date)"
                                        x-text="day.label"
                                    ></button>
                                </template>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-xs">
                                <button type="button" @click="clearDate" class="text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white">Clear</button>
                                <button type="button" @click="selectToday" class="text-brand-600 hover:text-brand-700">Today</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="service_type" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Service Type</label>
                    <select
                        id="service_type"
                        name="service_type"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                        onchange="updatePreview()"
                    >
                        @php($selectedServiceType = request('service_type', 'all'))
                        <option value="all" {{ $selectedServiceType === 'all' ? 'selected' : '' }}>All service types</option>
                        @forelse ($serviceTypes as $serviceType)
                            <option value="{{ $serviceType }}" {{ $selectedServiceType === $serviceType ? 'selected' : '' }}>
                                {{ $serviceType }}
                            </option>
                        @empty
                            <option value="all" disabled>No service types available</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Clear Filters
                </a>
                <div class="flex flex-wrap gap-3">
                    <form id="exportExcelForm" method="POST" action="{{ route('admin.export.excel') }}" style="display: inline;" data-global-loading="false">
                        @csrf
                        <input type="hidden" name="start_date" id="excelStartDate">
                        <input type="hidden" name="end_date" id="excelEndDate">
                        <input type="hidden" name="service_type" id="excelServiceType">
                        <button type="submit" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                            Export Excel
                        </button>
                    </form>
                    <form id="exportCsvForm" method="POST" action="{{ route('admin.export.csv') }}" style="display: inline;" data-global-loading="false">
                        @csrf
                        <input type="hidden" name="start_date" id="csvStartDate">
                        <input type="hidden" name="end_date" id="csvEndDate">
                        <input type="hidden" name="service_type" id="csvServiceType">
                        <button type="submit" class="rounded-xl bg-slate-600 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Export CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="previewContainer" class="space-y-6">
        <!-- Preview will be loaded here via AJAX -->
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Watch for date changes and trigger preview updates
    function watchDateInputs() {
        const startDateInput = document.querySelector('input[name="start_date"][type="hidden"]');
        const endDateInput = document.querySelector('input[name="end_date"][type="hidden"]');

        if (startDateInput && endDateInput) {
            const observer = new MutationObserver(() => {
                updatePreview();
            });

            observer.observe(startDateInput, { attributes: true });
            observer.observe(endDateInput, { attributes: true });
        }
    }

    // Get filter values from the form
    function getFilterValues() {
        const startDate = document.querySelector('input[name="start_date"][type="hidden"]')?.value || '';
        const endDate = document.querySelector('input[name="end_date"][type="hidden"]')?.value || '';
        const serviceType = document.querySelector('#service_type')?.value || '';
        return { startDate, endDate, serviceType };
    }

    // Update preview via AJAX
    async function updatePreview() {
        const filters = getFilterValues();
        const previewContainer = document.getElementById('previewContainer');

        try {
            const response = await fetch('{{ route('admin.reports.preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'text/html'
                },
                body: JSON.stringify({
                    start_date: filters.startDate,
                    end_date: filters.endDate,
                    service_type: filters.serviceType
                })
            });

            if (!response.ok) throw new Error('Preview failed');
            const html = await response.text();
            previewContainer.innerHTML = html;

            // Update hidden form fields for export
            document.getElementById('excelStartDate').value = filters.startDate;
            document.getElementById('excelEndDate').value = filters.endDate;
            document.getElementById('excelServiceType').value = filters.serviceType;
            document.getElementById('csvStartDate').value = filters.startDate;
            document.getElementById('csvEndDate').value = filters.endDate;
            document.getElementById('csvServiceType').value = filters.serviceType;
        } catch (error) {
            console.error('Error loading preview:', error);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        watchDateInputs();
        updatePreview();
    });
</script>
@endpush
