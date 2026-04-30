<div class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-10">
    <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
        <div class="space-y-2">
            <div class="skeleton h-7 w-48"></div>
            <div class="skeleton h-4 w-96 max-w-full"></div>
        </div>

        <div class="mt-8 space-y-6">
            @for ($i = 0; $i < 4; $i++)
                <div class="space-y-2">
                    <div class="skeleton h-4 w-32"></div>
                    <div class="skeleton h-11 w-full rounded-xl"></div>
                </div>
            @endfor

            <div class="flex items-center gap-3 pt-2">
                <div class="skeleton h-10 w-28 rounded-xl"></div>
                <div class="skeleton h-10 w-20 rounded-xl"></div>
            </div>
        </div>
    </div>
</div>
