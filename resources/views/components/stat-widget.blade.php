@props([
    'type' => '',
])

<div
    x-data="{
        loading: true,
        value: '',
        label: '',
        async init() {
            try {
                const response = await fetch(`/api/dashboard/stat?type={{ $type }}`, {
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Failed to load stat widget');
                }

                const data = await response.json();
                this.value = data.value ?? '';
                this.label = data.label ?? '';
            } catch (error) {
                this.label = 'Error loading widget';
                this.value = '—';
                console.error(error);
            } finally {
                this.loading = false;
            }
        }
    }"
    class="rounded-xl bg-white p-5 shadow-sm dark:bg-slate-900"
>
    <template x-if="loading">
        <div class="space-y-3">
            <div class="skeleton h-4 w-1/2"></div>
            <div class="skeleton h-8 w-3/4"></div>
        </div>
    </template>

    <template x-if="!loading">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400" x-text="label"></p>
            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white" x-text="value"></p>
        </div>
    </template>
</div>
