<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$controller = app(DashboardController::class);
$request = Request::create('/admin', 'GET');

$profileRun = function (string $label) use ($controller, $request): void {
    DB::flushQueryLog();
    DB::enableQueryLog();

    $start = microtime(true);
    $response = $controller->index($request);
    $elapsed = round((microtime(true) - $start) * 1000, 2);

    $queries = DB::getQueryLog();
    $queryCount = count($queries);
    $dbTime = round(array_sum(array_column($queries, 'time')), 2);

    echo "{$label}_total_ms={$elapsed}" . PHP_EOL;
    echo "{$label}_query_count={$queryCount}" . PHP_EOL;
    echo "{$label}_db_time_ms={$dbTime}" . PHP_EOL;

    foreach ($queries as $index => $query) {
        $sql = preg_replace('/\s+/', ' ', $query['query']);
        echo "{$label}_q" . ($index + 1) . "_ms=" . round((float) $query['time'], 2) . " | {$sql}" . PHP_EOL;
    }

    if ($response instanceof \Illuminate\View\View) {
        $renderStart = microtime(true);
        $response->render();
        $renderElapsed = round((microtime(true) - $renderStart) * 1000, 2);
        echo "{$label}_render_ms={$renderElapsed}" . PHP_EOL;
    }
};

$profileRun('cold');
$profileRun('warm');
