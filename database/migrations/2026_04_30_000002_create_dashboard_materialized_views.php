<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing views if any
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_activity_chart CASCADE');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_top_facilities CASCADE');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_top_services CASCADE');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_training_modes CASCADE');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_most_active_users CASCADE');

        // Activity chart: last 7 days
        DB::statement('
            CREATE MATERIALIZED VIEW mv_dashboard_activity_chart AS
            SELECT
                DATE(activity_at) AS activity_date,
                COUNT(*) AS total
            FROM tbl_activities
            WHERE activity_at >= NOW() - INTERVAL \'6 days\'
                AND activity_at IS NOT NULL
            GROUP BY DATE(activity_at)
            ORDER BY activity_date
        ');
        DB::statement('CREATE INDEX idx_mv_activity_chart_date ON mv_dashboard_activity_chart (activity_date)');

        // Top facilities
        DB::statement('
            CREATE MATERIALIZED VIEW mv_dashboard_top_facilities AS
            SELECT
                facility_used,
                COUNT(*) AS total
            FROM tbl_activities
            WHERE facility_used IS NOT NULL
                AND facility_used != \'\'
            GROUP BY facility_used
            ORDER BY total DESC
            LIMIT 6
        ');
        DB::statement('CREATE INDEX idx_mv_top_facilities_total ON mv_dashboard_top_facilities (total DESC)');

        // Top services
        DB::statement('
            CREATE MATERIALIZED VIEW mv_dashboard_top_services AS
            SELECT
                service_type,
                COUNT(*) AS total
            FROM tbl_activities
            WHERE service_type IS NOT NULL
                AND service_type != \'\'
            GROUP BY service_type
            ORDER BY total DESC
            LIMIT 6
        ');
        DB::statement('CREATE INDEX idx_mv_top_services_total ON mv_dashboard_top_services (total DESC)');

        // Training modes
        DB::statement('
            CREATE MATERIALIZED VIEW mv_dashboard_training_modes AS
            SELECT
                md_training,
                COUNT(*) AS total
            FROM tbl_activities
            WHERE md_training IS NOT NULL
                AND md_training != \'\'
            GROUP BY md_training
            ORDER BY total DESC
        ');
        DB::statement('CREATE INDEX idx_mv_training_modes_total ON mv_dashboard_training_modes (total DESC)');

        // Most active users
        DB::statement('
            CREATE MATERIALIZED VIEW mv_dashboard_most_active_users AS
            SELECT
                ru.user_id,
                TRIM(CONCAT(ru.fname_user, \' \', ru.lname_user)) AS name,
                COUNT(ta.activity_id) AS activities_count
            FROM tbl_regusers ru
            LEFT JOIN tbl_activities ta ON ru.user_id = ta.user_id
            GROUP BY ru.user_id, ru.fname_user, ru.lname_user
            ORDER BY activities_count DESC
            LIMIT 5
        ');
        DB::statement('CREATE INDEX idx_mv_active_users_count ON mv_dashboard_most_active_users (activities_count DESC)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_activity_chart CASCADE');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_top_facilities CASCADE');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_top_services CASCADE');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_training_modes CASCADE');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_dashboard_most_active_users CASCADE');
    }
};
