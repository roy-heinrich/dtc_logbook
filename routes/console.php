<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:purge-soft-deletes')->daily();
Schedule::command('cache:prune')->hourly();
Schedule::command('cache:clear')->dailyAt('03:00');
