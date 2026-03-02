<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Admin' }} | DTC Logbook</title>
    <style>
        html { color-scheme: light; }
        html.dark { color-scheme: dark; }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen shader-bg text-slate-900 dark:text-slate-100">
    <script>
        (function () {
            const theme = localStorage.getItem('theme');
            if (theme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <div x-data="{ open: false }" class="flex min-h-screen pt-20">
        @include('admin.partials.sidebar')

        <div class="flex min-h-screen flex-1 flex-col overflow-x-hidden">
            @include('admin.partials.topbar', ['pageTitle' => $pageTitle ?? 'Admin'])

            <main class="flex-1 px-6 pb-10 pt-6 lg:px-10 max-w-full">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800 dark:border-brand-700/60 dark:bg-brand-900/30 dark:text-brand-100">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            // Update toggle checkbox
            const themeToggle = document.querySelector('[data-theme-toggle]');
            if (themeToggle) {
                themeToggle.checked = isDark;
            }
        }
        
        // Initialize theme toggle checkbox state on page load
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggle = document.querySelector('[data-theme-toggle]');
            if (themeToggle) {
                themeToggle.checked = document.documentElement.classList.contains('dark');
            }
        });
    </script>
</body>
</html>
