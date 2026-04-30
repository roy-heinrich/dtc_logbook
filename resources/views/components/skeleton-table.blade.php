@props(['rows' => 5])

<!-- Skeleton Table -->
<div class="rounded-2xl glass-card shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-[640px] w-full">
            <thead class="bg-slate-50 dark:bg-slate-950">
                <tr>
                    <th class="px-6 py-3">
                        <div class="skeleton-text h-4 w-24 rounded"></div>
                    </th>
                    <th class="px-6 py-3">
                        <div class="skeleton-text h-4 w-20 rounded"></div>
                    </th>
                    <th class="px-6 py-3">
                        <div class="skeleton-text h-4 w-20 rounded"></div>
                    </th>
                    <th class="px-6 py-3">
                        <div class="skeleton-text h-4 w-16 rounded"></div>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @for ($i = 0; $i < $rows; $i++)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                <div class="skeleton-text h-4 w-32 rounded"></div>
                                <div class="skeleton-text h-3 w-16 rounded"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="skeleton-text h-4 w-24 rounded"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="skeleton-text h-4 w-20 rounded"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="skeleton-text h-4 w-16 rounded"></div>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
