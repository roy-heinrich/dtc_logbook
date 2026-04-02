<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$measure = function (string $label, callable $callback): void {
    $start = microtime(true);
    $callback();
    $elapsed = round((microtime(true) - $start) * 1000, 2);
    echo $label . '_ms=' . $elapsed . PHP_EOL;
};

$measure('db_ping', function (): void {
    Illuminate\Support\Facades\DB::select('select 1 as ok');
});

$measure('admin_guard_user', function (): void {
    $adminId = Illuminate\Support\Facades\DB::table('admins')->whereNull('deleted_at')->value('id');
    if ($adminId) {
        App\Models\Admin::with('role.permissions')->find($adminId);
    }
});

$measure('dashboard_latest', function (): void {
    App\Models\Activity::query()
        ->with(['user:user_id,fname_user,lname_user'])
        ->orderByDesc('activity_at')
        ->first();
});

$measure('reports_preview', function (): void {
    $query = App\Models\Activity::query()
        ->with(['user:user_id,fname_user,mname_user,lname_user,suffix_user,number_user,sector_user,birthdate,sex_user']);

    (clone $query)->orderByDesc('activity_at')->limit(10)->get();
    (clone $query)->distinct('user_id')->count('user_id');
    (clone $query)->count();
    (clone $query)->whereBetween('activity_at', [now()->startOfDay(), now()->endOfDay()])->count();
});
