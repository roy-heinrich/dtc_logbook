<x-guest-layout>
    <div class="mb-4 text-sm text-gray-700 dark:text-gray-300">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <form method="POST" action="{{ route('password.email') }}" data-loading-overlay="true" data-loading-text="Sending reset link..." data-loading-button-text="Sending...">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('login'))
                <a class="underline text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>
            @endif

            <x-primary-button>
                <span data-submit-text>{{ __('Email Password Reset Link') }}</span>
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
