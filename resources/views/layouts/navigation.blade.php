<nav x-data="stickyHeader()" 
     @scroll.window="handleScroll()"
    @keydown.escape.window="closeMobileMenu()"
     :class="[
         'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
         scrolled ? 'bg-white dark:bg-slate-900 sm:bg-white/85 sm:dark:bg-slate-900/85 sm:backdrop-blur-md border-b border-gray-200 dark:border-slate-700' : 'bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-700'
     ]"
     class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14 sm:h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-900 hover:text-gray-900 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" data-global-loading="false" @click.stop>
                            @csrf

                            <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" style="background: none; border: none; cursor: pointer;">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="toggleMobileMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-slate-800 focus:text-gray-900 dark:focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': ! mobileMenuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Hamburger Panel -->
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-cloak
        class="fixed top-14 inset-x-0 bottom-0 z-40 bg-slate-900 border-t border-slate-700 overflow-y-auto sm:hidden"
    >
        <div class="py-2 space-y-1 border-b border-slate-700">
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" @click="closeMobileMenu()">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" @click="closeMobileMenu()">
                {{ __('Profile') }}
            </x-responsive-nav-link>
        </div>

        <div class="px-4 py-4 border-b border-slate-700">
            <div class="font-medium text-base text-gray-100">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
        </div>

        <div class="py-2">
            <form method="POST" action="{{ route('logout') }}" data-global-loading="false" onsubmit="closeMobileMenu();">
                @csrf

                <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-red-400 hover:bg-slate-800 focus:outline-none focus:bg-slate-800 transition duration-150 ease-in-out font-medium" style="background: none; border: none; cursor: pointer;">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</nav>
