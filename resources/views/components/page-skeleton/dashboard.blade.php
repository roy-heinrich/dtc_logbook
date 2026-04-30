<div class="mx-auto flex h-full w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-10">
    <div class="grid gap-6 lg:grid-cols-[16rem_minmax(0,1fr)]">
        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
            <div class="flex items-center gap-3">
                <div class="skeleton h-12 w-12 rounded-full"></div>
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="skeleton h-4 w-24"></div>
                    <div class="skeleton h-3 w-36"></div>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @for ($i = 0; $i < 6; $i++)
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2">
                        <div class="skeleton h-10 w-10 rounded-xl"></div>
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="skeleton h-4 w-28"></div>
                            <div class="skeleton h-3 w-20"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
                <div class="flex items-center justify-between gap-4">
                    <div class="space-y-2">
                        <div class="skeleton h-6 w-56"></div>
                        <div class="skeleton h-4 w-80"></div>
                    </div>
                    <div class="skeleton h-10 w-28 rounded-xl"></div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 dark:border-slate-800 dark:bg-slate-950/50">
                            <div class="skeleton h-4 w-24"></div>
                            <div class="skeleton mt-4 h-8 w-20"></div>
                            <div class="skeleton mt-3 h-3 w-32"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="xl:col-span-2 rounded-2xl border border-slate-200/70 bg-white/70 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
                    <div class="skeleton h-6 w-40"></div>
                    <div class="mt-6 space-y-4">
                        <div class="skeleton h-56 w-full rounded-2xl"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
                    <div class="skeleton h-6 w-44"></div>
                    <div class="mt-6 space-y-4">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="flex items-center gap-3">
                                <div class="skeleton h-10 w-10 rounded-full"></div>
                                <div class="min-w-0 flex-1 space-y-2">
                                    <div class="skeleton h-4 w-3/4"></div>
                                    <div class="skeleton h-3 w-1/2"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
