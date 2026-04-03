<?php

namespace App\Console\Commands;

use App\Services\DashboardSnapshotService;
use Illuminate\Console\Command;

class BuildDashboardSnapshot extends Command
{
    protected $signature = 'app:build-dashboard-snapshot';
    protected $description = 'Precompute the default dashboard summary for faster page loads';

    public function __construct(private readonly DashboardSnapshotService $dashboardSnapshotService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->dashboardSnapshotService->refresh();

        $this->info('Dashboard snapshot rebuilt.');

        return self::SUCCESS;
    }
}
