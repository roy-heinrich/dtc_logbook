<!-- Skeleton Chart Placeholder -->
<div class="rounded-2xl glass-card shadow-sm overflow-hidden">
    <div class="p-6">
        <div class="skeleton-text h-5 w-40 rounded mb-4"></div>
        <div class="space-y-3 mt-6">
            @for ($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3">
                    <div class="skeleton-text h-3 w-3 rounded-full"></div>
                    <div class="skeleton-text h-3 flex-1 rounded"></div>
                </div>
            @endfor
        </div>
    </div>
    <div class="h-64 bg-gradient-to-br from-slate-100 to-slate-50 dark:from-slate-900 dark:to-slate-800 skeleton-text"></div>
</div>
