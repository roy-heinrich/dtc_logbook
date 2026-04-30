@props([
    'endpoint' => '',
    'title' => 'Table',
])

<div
    x-data="{
        loading: true,
        rows: [],
        async init() {
            try {
                const response = await fetch(`{{ $endpoint }}`, {
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Failed to load table widget');
                }

                this.rows = await response.json();
            } catch (error) {
                console.error(error);
                this.rows = [];
            } finally {
                this.loading = false;
            }
        }
    }"
    class="rounded-xl bg-white p-5 shadow-sm dark:bg-slate-900"
>
    <h3 class="mb-4 text-lg font-semibold text-slate-900 dark:text-white" x-text="'{{ $title }}'"></h3>

    <template x-if="loading">
        <div class="space-y-3">
            <template x-for="i in 5" :key="i">
                <div class="flex gap-3">
                    <div class="skeleton h-4 w-1/4"></div>
                    <div class="skeleton h-4 w-1/3"></div>
                    <div class="skeleton h-4 w-1/5"></div>
                </div>
            </template>
        </div>
    </template>

    <template x-if="!loading">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-400 dark:border-slate-800">
                    <th class="pb-2">Order</th>
                    <th class="pb-2">Customer</th>
                    <th class="pb-2">Amount</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="row in rows" :key="row.id">
                    <tr class="border-b last:border-0 dark:border-slate-800">
                        <td class="py-2" x-text="row.order_number"></td>
                        <td class="py-2" x-text="row.customer"></td>
                        <td class="py-2" x-text="row.amount"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </template>
</div>
