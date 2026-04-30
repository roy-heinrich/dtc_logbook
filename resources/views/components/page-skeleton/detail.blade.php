<div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-10">
    <div class="grid gap-6 lg:grid-cols-[18rem_minmax(0,1fr)]">
        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
            <div class="flex items-center gap-3">
                <div class="skeleton h-16 w-16 rounded-full"></div>
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="skeleton h-5 w-32"></div>
                    <div class="skeleton h-4 w-24"></div>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @for ($i = 0; $i < 4; $i++)
                    <div class="skeleton h-4 w-full"></div>
                @endfor
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
                <div class="skeleton h-6 w-56"></div>
                <div class="mt-4 space-y-3">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="skeleton h-4 w-full"></div>
                    @endfor
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
                <div class="skeleton h-6 w-44"></div>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="rounded-xl border border-slate-200/70 bg-white/80 p-4 dark:border-slate-800 dark:bg-slate-950/50">
                            <div class="skeleton h-4 w-24"></div>
                            <div class="skeleton mt-3 h-8 w-28"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>