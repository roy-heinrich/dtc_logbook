<aside
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-slate-200 bg-white px-4 py-6 shadow-xl transition-transform duration-300 ease-in-out dark:border-slate-800 dark:bg-slate-900"
    :class="{'translate-x-0': open}"
>
    <div class="flex items-center justify-end">
        <button
            type="button"
            class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            @click="open = false"
            aria-label="Close sidebar"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="mt-8 text-xs font-semibold uppercase tracking-widest text-slate-400">
        Admin Menu
    </div>

    <nav class="mt-4 space-y-2">
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
        >
            <span>Dashboard</span>
        </a>
        <a
            href="{{ route('admin.login-logs.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.login-logs.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
        >
            <span>Login Logs</span>
        </a>
        <a
            href="{{ route('admin.regusers.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.regusers.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
        >
            <span>Registered Users</span>
        </a>
        <a
            href="{{ route('admin.reports.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
        >
            <span>Reports</span>
        </a>
        <a
            href="{{ route('admin.facilities.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.facilities.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
        >
            <span>Facilities</span>
        </a>
        <a
            href="{{ route('admin.services.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.services.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
        >
            <span>Services</span>
        </a>

        @if(auth('admin')->check() && auth('admin')->user()->isSuperAdmin())
            <div class="mt-6 text-xs font-semibold uppercase tracking-widest text-slate-400">
                Super Admin
            </div>
            <a
                href="{{ route('admin.admins.index') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.admins.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            >
                <span>Manage Admins</span>
            </a>
            <a
                href="{{ route('admin.roles.index') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('admin.roles.*') ? 'bg-brand-500 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
            >
                <span>Manage Roles</span>
            </a>
        @endif
    </nav>
</aside>

<div
    class="fixed inset-0 z-30 bg-slate-900/60 transition-opacity duration-300"
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="open = false"
    style="display: none;"
></div>
