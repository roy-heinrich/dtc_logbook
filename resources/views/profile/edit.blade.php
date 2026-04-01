<x-app-layout>
    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (auth('admin')->check() && auth('admin')->user()->must_change_password)
                <div class="p-4 sm:p-5 rounded-lg border border-amber-300 bg-amber-50 text-amber-900 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-200">
                    <h3 class="text-sm font-semibold">Important Notice</h3>
                    <p class="mt-1 text-sm">
                        This is your required first-login password update page. Once you leave after updating your password, you will no longer be required to access this page again.
                    </p>
                </div>
            @endif

            <div class="p-4 sm:p-8 glass-card shadow sm:rounded-lg">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 glass-card shadow sm:rounded-lg">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 glass-card shadow sm:rounded-lg">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
