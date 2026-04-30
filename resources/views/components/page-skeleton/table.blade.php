<div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-10">
    <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
        <div class="flex items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="skeleton h-6 w-56"></div>
                <div class="skeleton h-4 w-80"></div>
            </div>
            <div class="skeleton h-10 w-32 rounded-xl"></div>
        </div>

        <div class="mt-6 space-y-3">
            <div class="grid grid-cols-12 gap-3">
                <div class="skeleton h-4 col-span-3"></div>
                <div class="skeleton h-4 col-span-3"></div>
                <div class="skeleton h-4 col-span-2"></div>
                <div class="skeleton h-4 col-span-2"></div>
                <div class="skeleton h-4 col-span-2"></div>
            </div>

            @for ($i = 0; $i < 8; $i++)
                <div class="grid grid-cols-12 gap-3 border-t border-slate-200/60 pt-4 dark:border-slate-800">
                    <div class="skeleton h-4 col-span-3"></div>
                    <div class="skeleton h-4 col-span-3"></div>
                    <div class="skeleton h-4 col-span-2"></div>
                    <div class="skeleton h-4 col-span-2"></div>
                    <div class="skeleton h-4 col-span-2"></div>
                </div>
            @endfor
        </div>
    </div>
</div>
