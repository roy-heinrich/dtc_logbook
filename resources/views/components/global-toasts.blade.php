@php
    $toasts = [];

    if (session('status')) {
        $statusMessage = match (session('status')) {
            'profile-updated' => 'Profile updated successfully.',
            'password-updated' => 'Password updated successfully.',
            'verification-link-sent' => 'A new verification link has been sent to your email address.',
            default => (string) session('status'),
        };

        $toasts[] = [
            'type' => 'success',
            'message' => $statusMessage,
        ];
    }

    if (session('success')) {
        $toasts[] = [
            'type' => 'success',
            'message' => (string) session('success'),
        ];
    }

    if (session('error')) {
        $toasts[] = [
            'type' => 'error',
            'message' => (string) session('error'),
        ];
    }

    if (session('warning')) {
        $toasts[] = [
            'type' => 'warning',
            'message' => (string) session('warning'),
        ];
    }

    if (session('info')) {
        $toasts[] = [
            'type' => 'info',
            'message' => (string) session('info'),
        ];
    }

    if (session('message')) {
        $toasts[] = [
            'type' => 'info',
            'message' => (string) session('message'),
        ];
    }
@endphp

@if (! empty($toasts))
    <style>
        #global-toast-container {
            position: fixed;
            top: 16px;
            right: 16px;
            left: auto;
            z-index: 9999;
            width: 100%;
            max-width: 24rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            pointer-events: none;
        }

        .global-toast-item {
            pointer-events: auto;
            border-width: 1px;
            border-style: solid;
            border-radius: 0.75rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.22);
            opacity: 1;
            transform: translateX(0);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .global-toast-item[data-type="success"] {
            background: #ecfdf5;
            color: #065f46;
            border-color: #6ee7b7;
        }

        .global-toast-item[data-type="error"] {
            background: #fff1f2;
            color: #881337;
            border-color: #fda4af;
        }

        .global-toast-item[data-type="warning"] {
            background: #fffbeb;
            color: #92400e;
            border-color: #fcd34d;
        }

        .global-toast-item[data-type="info"] {
            background: #eff6ff;
            color: #1e3a8a;
            border-color: #93c5fd;
        }

        .dark .global-toast-item[data-type="success"] {
            background: #14532d;
            color: #d1fae5;
            border-color: #166534;
        }

        .dark .global-toast-item[data-type="error"] {
            background: #881337;
            color: #ffe4e6;
            border-color: #9f1239;
        }

        .dark .global-toast-item[data-type="warning"] {
            background: #78350f;
            color: #fef3c7;
            border-color: #92400e;
        }

        .dark .global-toast-item[data-type="info"] {
            background: #1e3a8a;
            color: #dbeafe;
            border-color: #1d4ed8;
        }
    </style>

    <div id="global-toast-container">
        @foreach ($toasts as $index => $toast)
            @php $type = $toast['type']; @endphp

            <div
                data-toast
                data-type="{{ $type }}"
                class="global-toast-item"
                data-toast-delay="5000"
            >
                <div class="flex items-start gap-3 px-4 py-3">
                    <div class="mt-0.5 shrink-0">
                        @if ($type === 'error')
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 114.293 4.293a8 8 0 0113.414 5.414zM9 5a1 1 0 112 0v4a1 1 0 11-2 0V5zm1 10a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 15z" clip-rule="evenodd"/></svg>
                        @elseif ($type === 'warning')
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l5.58 9.92c.75 1.334-.213 2.981-1.742 2.981H4.42c-1.53 0-2.492-1.647-1.742-2.98l5.58-9.921zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        @elseif ($type === 'info')
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 11.293 4.293A8 8 0 0118 10zm-9-3a1 1 0 112 0 1 1 0 01-2 0zm2 8a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8.5 12.086 6.707 10.293a1 1 0 10-1.414 1.414l2.5 2.5a1 1 0 001.414 0l7.5-7.5a1 1 0 000-1.414z" clip-rule="evenodd"/></svg>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1 text-sm font-medium leading-5">
                        {{ $toast['message'] }}
                    </div>

                    <button
                        type="button"
                        data-toast-close
                        class="-mr-1 -mt-1 rounded-md p-1.5 text-current transition hover:bg-black/10 dark:hover:bg-white/10"
                        aria-label="Dismiss notification"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('global-toast-container');

            if (!container) {
                return;
            }

            const hideToast = (toast) => {
                if (!toast || toast.dataset.closing === '1') {
                    return;
                }

                toast.dataset.closing = '1';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(24px)';

                window.setTimeout(() => {
                    toast.remove();
                    if (!container.querySelector('[data-toast]')) {
                        container.remove();
                    }
                }, 300);
            };

            container.querySelectorAll('[data-toast]').forEach((toast) => {
                requestAnimationFrame(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateX(0)';
                });

                const delay = Number(toast.dataset.toastDelay || 5000);
                window.setTimeout(function () {
                    hideToast(toast);
                }, delay);

                const closeButton = toast.querySelector('[data-toast-close]');
                if (closeButton) {
                    closeButton.addEventListener('click', function () {
                        hideToast(toast);
                    });
                }
            });
        });
    </script>
@endif
