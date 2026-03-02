<?php

namespace App\Providers;

use App\Models\LoginLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            if (! $event->user) {
                return;
            }

            // Queue the login log to avoid blocking the login process
            dispatch(function () use ($event) {
                LoginLog::create([
                    'user_id' => $event->user->getAuthIdentifier(),
                    'user_type' => get_class($event->user),
                    'login_at' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            })->afterResponse();
        });
    }
}
