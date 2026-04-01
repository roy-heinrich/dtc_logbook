<header x-data="stickyHeader()"
        @scroll.window="handleScroll()"
        :class="[
            scrolled ? 'bg-[#dfe4e9] dark:bg-slate-950/60 border-slate-300 dark:border-slate-800' : 'bg-[#dfe4e9] dark:bg-slate-950/80 border-slate-300 dark:border-slate-800',
            hidden ? '-translate-y-full' : 'translate-y-0'
        ]"
        class="fixed top-0 right-0 z-50 border-b border-slate-300 bg-[#dfe4e9] px-6 py-4 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80 transition-all duration-300 lg:px-10"
        style="left: 5.5rem;">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center">
                    <img src="/images/logo.png" alt="{{ $pageTitle }}" class="h-14 w-14 rounded-full object-cover" fetchpriority="high" data-priority="high" loading="eager" decoding="sync">
                </a>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @auth
                <div class="hidden items-center gap-2 text-sm text-slate-500 dark:text-slate-400 sm:flex">
                    <span class="text-slate-900 dark:text-white">{{ auth()->user()->name }}</span>
                    <span class="text-xs">{{ auth()->user()->email }}</span>
                </div>
            @endauth
            <button
                type="button"
                aria-label="Color Mode"
                data-theme-toggle
                onclick="toggleTheme()"
                class="flex justify-center p-2 text-gray-500 transition duration-150 ease-in-out bg-gray-100 border border-transparent rounded-md lg:bg-white lg:dark:bg-gray-900 dark:text-gray-200 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50"
            >
                <svg data-theme-icon-sun xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 hidden">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
                <svg data-theme-icon-moon xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 hidden">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
            </button>
        </div>
    </div>
</header>
