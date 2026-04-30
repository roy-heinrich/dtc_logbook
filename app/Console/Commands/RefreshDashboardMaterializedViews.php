<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshDashboardMaterializedViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:refresh-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh dashboard materialized views for better performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->info('Refreshing dashboard materialized views...');

            DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mv_dashboard_activity_chart');
            $this->line('✓ Refreshed mv_dashboard_activity_chart');

            DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mv_dashboard_top_facilities');
            $this->line('✓ Refreshed mv_dashboard_top_facilities');

            DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mv_dashboard_top_services');
            $this->line('✓ Refreshed mv_dashboard_top_services');

            DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mv_dashboard_training_modes');
            $this->line('✓ Refreshed mv_dashboard_training_modes');

            DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mv_dashboard_most_active_users');
            $this->line('✓ Refreshed mv_dashboard_most_active_users');

            $this->info('Dashboard materialized views refreshed successfully!');

            Log::info('Dashboard materialized views refreshed');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error('Failed to refresh materialized views: ' . $exception->getMessage());
            Log::error('Dashboard materialized views refresh failed', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
