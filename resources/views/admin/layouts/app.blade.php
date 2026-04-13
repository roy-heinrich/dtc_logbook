<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.social-meta')
    <title>{{ $pageTitle ?? 'Admin' }} | DTC Logbook</title>
    
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
<body class="min-h-screen shader-bg text-slate-700 dark:text-slate-100">
    @include('components.global-toasts')

    <div
        id="global-submit-loading-overlay"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm"
        role="status"
        aria-live="polite"
        aria-hidden="true"
    >
        <div class="w-[min(20rem,calc(100vw-2rem))] rounded-xl border border-slate-200 bg-white p-5 text-center shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
            <p id="global-submit-loading-title" class="mt-4 text-base font-semibold text-slate-900 dark:text-slate-100">Processing your Request...</p>
            <p class="mt-1 text-xs text-slate-700 dark:text-slate-300">Please wait while we process your request.</p>
        </div>
    </div>

    <div class="flex min-h-screen pt-20">
        @include('admin.partials.sidebar')

        <div class="flex min-h-screen flex-1 flex-col overflow-x-hidden" style="margin-left: 5.5rem;">
            @include('admin.partials.topbar', ['pageTitle' => $pageTitle ?? 'Admin'])

            <main class="flex-1 px-4 sm:px-6 pb-10 pt-6 lg:px-10 max-w-full">
                @yield('content')
            </main>
        </div>
    </div>

    <div
        id="global-action-confirm-modal"
        class="fixed inset-0 z-[10000] hidden items-center justify-center bg-slate-950/70 px-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="global-action-confirm-title"
    >
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-500/10 text-rose-500 dark:bg-rose-500/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M7.938 4h8.124c1.54 0 2.502 1.667 1.732 3L13.732 14a2 2 0 01-3.464 0L6.206 7c-.77-1.333.192-3 1.732-3z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 id="global-action-confirm-title" class="text-lg font-semibold text-slate-900 dark:text-slate-100">Confirm Action</h3>
                    <p id="global-action-confirm-message" class="mt-2 text-sm text-slate-600 dark:text-slate-300">Are you sure you want to continue?</p>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    data-confirm-cancel
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    data-confirm-submit
                    class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700"
                >
                    Confirm
                </button>
            </div>
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

        (() => {
            const modal = document.getElementById('global-action-confirm-modal');
            if (!modal) {
                return;
            }

            const titleElement = document.getElementById('global-action-confirm-title');
            const messageElement = document.getElementById('global-action-confirm-message');
            const submitButton = modal.querySelector('[data-confirm-submit]');
            const cancelButtons = modal.querySelectorAll('[data-confirm-cancel]');
            let pendingForm = null;

            const openModal = ({ title, message, submitText, isDanger }) => {
                titleElement.textContent = title;
                messageElement.textContent = message;
                submitButton.textContent = submitText;
                submitButton.classList.toggle('bg-rose-600', isDanger);
                submitButton.classList.toggle('hover:bg-rose-700', isDanger);
                submitButton.classList.toggle('bg-brand-500', !isDanger);
                submitButton.classList.toggle('hover:bg-brand-600', !isDanger);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                requestAnimationFrame(() => cancelButtons[0]?.focus());
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
                pendingForm = null;
            };

            cancelButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            submitButton.addEventListener('click', () => {
                if (!pendingForm) {
                    closeModal();
                    return;
                }

                const formToSubmit = pendingForm;
                closeModal();
                formToSubmit.submit();
            });

            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                if (form.dataset.skipConfirm === 'true' || form.dataset.confirmed === 'true') {
                    return;
                }

                const methodOverride = form.querySelector('input[name="_method"]')?.value;
                const method = (methodOverride || form.getAttribute('method') || 'GET').toUpperCase();
                const action = (form.getAttribute('action') || '').toLowerCase();

                const hasExplicitConfig =
                    form.hasAttribute('data-confirm-title') ||
                    form.hasAttribute('data-confirm-message') ||
                    form.hasAttribute('data-confirm-submit');

                const isDeleteAction = method === 'DELETE' || action.includes('destroy') || action.includes('force-delete');

                if (!hasExplicitConfig && !isDeleteAction) {
                    return;
                }

                event.preventDefault();

                const isPermanentDelete = action.includes('force-delete') || form.dataset.confirmType === 'permanent-delete';
                const isDanger = isDeleteAction || form.dataset.confirmDanger === 'true';

                const title = form.dataset.confirmTitle || (isPermanentDelete ? 'Permanently Delete Item?' : 'Delete Item?');
                const message = form.dataset.confirmMessage || (isPermanentDelete
                    ? 'This action is permanent and cannot be undone.'
                    : 'This item will be moved to trash. You can restore it later.');
                const submitText = form.dataset.confirmSubmit || (isPermanentDelete ? 'Delete Permanently' : 'Delete');

                pendingForm = form;
                openModal({ title, message, submitText, isDanger });
            }, true);
        })();
    </script>
</body>
</html>
