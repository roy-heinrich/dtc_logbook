<x-guest-layout>
    <!-- Session Status -->
    <form method="POST" action="{{ route('login') }}" data-loading-overlay="true" data-loading-text="Logging in...">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <p id="caps-lock-warning" class="mt-2 hidden text-sm font-medium text-amber-600 dark:text-amber-400" role="status" aria-live="polite">
                Caps Lock is on.
            </p>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        (() => {
            const passwordInput = document.getElementById('password');
            const capsLockWarning = document.getElementById('caps-lock-warning');

            if (!passwordInput || !capsLockWarning) {
                return;
            }

            let isPasswordFocused = false;

            const setWarningVisible = (visible) => {
                capsLockWarning.classList.toggle('hidden', !visible);
            };

            const updateCapsWarning = (event) => {
                const capsOn = Boolean(event.getModifierState && event.getModifierState('CapsLock'));
                if (isPasswordFocused) {
                    setWarningVisible(capsOn);
                }
            };

            passwordInput.addEventListener('keydown', updateCapsWarning);
            passwordInput.addEventListener('keyup', updateCapsWarning);
            passwordInput.addEventListener('focus', () => {
                isPasswordFocused = true;
            });
            passwordInput.addEventListener('blur', () => {
                isPasswordFocused = false;
                setWarningVisible(false);
            });

            document.addEventListener('keydown', updateCapsWarning);
            document.addEventListener('keyup', updateCapsWarning);
        })();
    </script>
</x-guest-layout>
