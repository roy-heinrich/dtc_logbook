<aside
    x-data="{ expanded: false }"
    @mouseenter="expanded = true"
    @mouseleave="expanded = false"
    class="fixed inset-y-0 left-0 border-r border-slate-300 bg-[#dde2e7] px-3 py-6 shadow-xl transition-all duration-300 ease-in-out dark:border-slate-800 dark:bg-slate-900 translate-x-0 overflow-hidden"
    style="z-index: 9999 !important; width: 5.5rem;"
    x-init="$watch('expanded', value => { $el.style.width = value ? '16rem' : '5.5rem' })"
>
    <div class="text-xs font-semibold uppercase tracking-widest text-slate-400 transition-all duration-300"
         :class="expanded ? 'opacity-100' : 'opacity-0 w-0 max-w-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>
        <span class="whitespace-nowrap">Admin Menu</span>
    </div>

    <nav class="mt-4 space-y-2">
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            title="Dashboard"
        >
            <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Dashboard</span>
        </a>
        <a
            href="{{ route('admin.login-logs.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.login-logs.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            title="Login Logs"
        >
            <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Login Logs</span>
        </a>
        <a
            href="{{ route('admin.regusers.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.regusers.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            title="Registered Users"
        >
            <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Registered Users</span>
        </a>
        <a
            href="{{ route('admin.reports.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            title="Reports"
        >
            <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Reports</span>
        </a>
        <a
            href="{{ route('admin.facilities.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.facilities.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            title="Facilities"
        >
            <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Facilities</span>
        </a>
        <a
            href="{{ route('admin.services.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.services.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            title="Services"
        >
            <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Services</span>
        </a>
        <a
            href="{{ route('admin.agreements.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.agreements.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            title="Privacy & TOS"
        >
            <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Privacy & TOS</span>
        </a>

        @if(auth('admin')->check() && auth('admin')->user()->isSuperAdmin())
            <div class="mt-6 text-xs font-semibold uppercase tracking-widest text-slate-400 transition-all duration-300"
                 :class="expanded ? 'opacity-100' : 'opacity-0 w-0 max-w-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>
                <span class="whitespace-nowrap">Super Admin</span>
            </div>
            <a
                href="{{ route('admin.admins.index') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.admins.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
                title="Manage Admins"
            >
                <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Manage Admins</span>
            </a>
            <a
                href="{{ route('admin.roles.index') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.roles.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
                title="Manage Roles"
            >
                <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Manage Roles</span>
            </a>
        @endif

        @auth
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
                    title="Logout"
                >
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
                    </svg>
                    <span class="whitespace-nowrap transition-all duration-300" :class="expanded ? 'ml-3 opacity-100' : 'w-0 max-w-0 opacity-0 overflow-hidden'" x-show="expanded" x-transition x-cloak>Logout</span>
                </button>
            </form>
        @endauth
    </nav>
</aside>
