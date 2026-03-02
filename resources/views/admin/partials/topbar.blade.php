<header x-data="stickyHeader()"
        @scroll.window="handleScroll()"
        :class="[
            'fixed top-0 right-0 left-0 z-50 border-b px-6 py-4 backdrop-blur transition-all duration-300 lg:px-10',
            scrolled ? 'bg-white/60 dark:bg-slate-950/60 border-slate-200 dark:border-slate-800' : 'bg-white/90 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800',
            hidden ? '-translate-y-full' : 'translate-y-0'
        ]"
        class="fixed top-0 right-0 left-0 z-50 border-b border-slate-200 bg-white/90 px-6 py-4 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80 lg:px-10">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <button
                type="button"
                class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                @click="open = !open"
                aria-label="Toggle sidebar"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center">
                    <img src="/images/logo.png" alt="{{ $pageTitle }}" class="h-14 w-14 rounded-full object-cover">
                </a>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <label class="ui-switch" aria-label="Toggle theme">
                <input type="checkbox" data-theme-toggle onchange="toggleTheme()" />
                <div class="slider">
                    <div class="circle"></div>
                </div>
            </label>
            @auth
                <div class="hidden items-center gap-2 text-sm text-slate-500 dark:text-slate-400 sm:flex">
                    <span class="text-slate-900 dark:text-white">{{ auth()->user()->name }}</span>
                    <span class="text-xs">{{ auth()->user()->email }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="rounded-xl border border-red-300 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-600 transition hover:bg-red-50 dark:border-red-700 dark:text-red-400 dark:hover:bg-red-900/20">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</header>
