@props([
    'endpoint' => '',
    'canvasId' => 'dashboardChart',
    'title' => 'Chart',
])

<div
    x-data="{
        loading: true,
        chartData: null,
        async init() {
            try {
                const response = await fetch(`{{ $endpoint }}`, {
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Failed to load chart widget');
                }

                this.chartData = await response.json();
            } catch (error) {
                console.error(error);
                this.chartData = null;
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    if (this.chartData && window.renderDashboardChart) {
                        window.renderDashboardChart('{{ $canvasId }}', this.chartData);
                    }
                });
            }
        }
    }"
    class="rounded-xl bg-white p-5 shadow-sm dark:bg-slate-900"
>
    <h3 class="mb-4 text-lg font-semibold text-slate-900 dark:text-white" x-text="'{{ $title }}'"></h3>

    <template x-if="loading">
        <div class="space-y-2">
            <div class="skeleton h-4 w-1/3"></div>
            <div class="skeleton h-48 w-full"></div>
        </div>
    </template>

    <canvas x-show="!loading" x-cloak :id="'{{ $canvasId }}'" class="h-48 w-full"></canvas>
</div>
