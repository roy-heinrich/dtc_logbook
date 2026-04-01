<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('partials.social-meta')

        <title>{{ config('app.name', 'Tech4ED Logbook|Admin') }}</title>
        
        <link rel="preload" as="image" href="{{ asset('images/background.webp') }}">

        <style>
            html.theme-preload *,
            html.theme-preload *::before,
            html.theme-preload *::after {
                transition: none !important;
                animation: none !important;
            }
        </style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        </noscript>

        <script>
            (function () {
                document.documentElement.classList.add('theme-preload');
                const theme = localStorage.getItem('theme');
                if (theme === 'light') {
                    document.documentElement.classList.remove('dark');
                } else {
                    document.documentElement.classList.add('dark');
                }
                requestAnimationFrame(() => {
                    document.documentElement.classList.remove('theme-preload');
                });
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased shader-bg text-gray-900 dark:text-gray-100" style="--shader-bg-image: url('{{ asset('images/background.webp') }}');">
        @include('components.global-toasts')

        <div class="min-h-screen pt-16">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>
    </body>
</html>
