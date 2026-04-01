<x-guest-layout>
    <div class="mb-4 text-sm text-gray-700 dark:text-gray-300">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" data-loading-overlay="true" data-loading-text="Confirming password..." data-loading-button-text="Confirming...">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                <span data-submit-text>{{ __('Confirm') }}</span>
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
