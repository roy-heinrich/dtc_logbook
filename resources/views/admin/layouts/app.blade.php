<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.social-meta')
    <title>{{ $pageTitle ?? 'Admin' }} | DTC Logbook</title>
    
    <link rel="preload" as="image" href="{{ asset('images/background.webp') }}">

    <style>
        html.theme-preload *,
        html.theme-preload *::before,
        html.theme-preload *::after {
            transition: none !important;
            animation: none !important;
        }
    </style>

    <script>
        (function () {
            document.documentElement.classList.add('theme-preload');
            const theme = localStorage.getItem('theme');
            const isDark = theme !== 'light';
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            requestAnimationFrame(() => {
                document.documentElement.classList.remove('theme-preload');
            });
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    </noscript>

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen shader-bg text-slate-700 dark:text-slate-100" style="--shader-bg-image: url('{{ asset('images/background.webp') }}');">
    @include('components.global-toasts')

    <div class="flex min-h-screen pt-20">
        @include('admin.partials.sidebar')

        <div class="flex min-h-screen flex-1 flex-col overflow-x-hidden" style="margin-left: 5.5rem;">
            @include('admin.partials.topbar', ['pageTitle' => $pageTitle ?? 'Admin'])

            <main class="flex-1 px-4 sm:px-6 pb-10 pt-6 lg:px-10 max-w-full">
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

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
