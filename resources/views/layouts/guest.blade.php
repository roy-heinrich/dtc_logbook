<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tech4ED Logbook|Admin') }}</title>
        @include('partials.social-meta')
        
        <link rel="preload" as="image" href="{{ asset('images/login_background.webp') }}">
        
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
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased login-bg" style="--login-bg-image: url('{{ asset('images/login_background.webp') }}');">
        @include('components.global-toasts')

        <div id="guest-page-content" class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <x-application-logo class="w-56 h-56 fill-current text-gray-700 dark:text-gray-400" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 glass-card shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        <div
            id="guest-submit-loading-overlay"
            class="fixed inset-0 z-[9999] hidden items-center justify-center pointer-events-auto select-none cursor-wait"
            style="height: 100dvh; width: 100vw; background: rgba(15, 23, 42, 0.78); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
            role="status"
            aria-live="polite"
            aria-hidden="true"
        >
            <div class="w-[min(19rem,calc(100vw-2rem))] rounded-xl border border-slate-200 bg-white p-5 text-center shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
                <p id="guest-submit-loading-title" class="mt-4 text-base font-semibold text-slate-900 dark:text-slate-100">Please wait...</p>
                <p class="mt-1 text-xs text-slate-700 dark:text-slate-300">We are processing your request.</p>
            </div>
        </div>

        <script>
            (() => {
                const loadingOverlay = document.getElementById('guest-submit-loading-overlay');
                const loadingTitle = document.getElementById('guest-submit-loading-title');
                const pageContent = document.getElementById('guest-page-content');

                if (!loadingOverlay || !loadingTitle || !pageContent) {
                    return;
                }

                document.addEventListener('submit', async (event) => {
                    const submittedForm = event.target;

                    if (!(submittedForm instanceof HTMLFormElement)) {
                        return;
                    }

                    if (submittedForm.dataset.loadingOverlay !== 'true') {
                        return;
                    }

                    if (submittedForm.dataset.submitting === 'true') {
                        event.preventDefault();
                        return;
                    }

                    const awaitSuccess = submittedForm.dataset.awaitSuccess === 'true';
                    if (awaitSuccess) {
                        event.preventDefault();
                    }

                    submittedForm.dataset.submitting = 'true';

                    loadingTitle.textContent = submittedForm.dataset.loadingText || 'Please wait...';
                    loadingOverlay.classList.remove('hidden');
                    loadingOverlay.classList.add('flex');
                    loadingOverlay.setAttribute('aria-hidden', 'false');
                    pageContent.classList.add('pointer-events-none');
                    pageContent.setAttribute('aria-hidden', 'true');
                    pageContent.inert = true;
                    document.body.classList.add('overflow-hidden');

                    const submitButton = submittedForm.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitButton instanceof HTMLButtonElement || submitButton instanceof HTMLInputElement) {
                        submitButton.disabled = true;
                    }

                    const buttonLabel = submittedForm.dataset.loadingButtonText;
                    if (!buttonLabel) {
                        if (!awaitSuccess) {
                            return;
                        }
                    }

                    if (buttonLabel && submitButton instanceof HTMLButtonElement) {
                        const textTarget = submitButton.querySelector('[data-submit-text]');
                        if (textTarget) {
                            textTarget.textContent = buttonLabel;
                        } else {
                            submitButton.textContent = buttonLabel;
                        }
                    }

                    if (buttonLabel && submitButton instanceof HTMLInputElement) {
                        submitButton.value = buttonLabel;
                    }

                    if (!awaitSuccess) {
                        return;
                    }

                    try {
                        const response = await fetch(submittedForm.action, {
                            method: submittedForm.method || 'POST',
                            body: new FormData(submittedForm),
                            credentials: 'same-origin',
                            redirect: 'follow',
                        });

                        const currentPath = new URL(window.location.href).pathname;
                        const finalPath = new URL(response.url, window.location.origin).pathname;
                        const stayedOnSamePage = finalPath === currentPath;

                        if (stayedOnSamePage) {
                            window.location.assign(response.url);
                            return;
                        }

                        loadingTitle.textContent = submittedForm.dataset.successText || 'Success';

                        const successDelay = Number.parseInt(submittedForm.dataset.successDelay || '600', 10);
                        const delay = Number.isFinite(successDelay) ? successDelay : 600;

                        setTimeout(() => {
                            window.location.assign(response.url);
                        }, delay);
                    } catch (error) {
                        submittedForm.submit();
                    }
                });
            })();

        </script>
    </body>
</html>
