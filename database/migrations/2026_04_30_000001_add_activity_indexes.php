<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddActivityIndexes extends Migration
{
    /**
     * Disable wrapping this migration in a transaction because
     * Postgres `CREATE INDEX CONCURRENTLY` cannot run inside a transaction.
     *
     * @var bool
     */
    public $withinTransaction = false;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        // For Postgres we can use IF NOT EXISTS to avoid duplicate index errors.
        if ($driver === 'pgsql') {
            // Use CONCURRENTLY to avoid locking issues; IF NOT EXISTS prevents duplicates.
            try {
                DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS tbl_activities_activity_at_index ON tbl_activities (activity_at)');
            } catch (\Throwable $e) {
                // ignore and continue
            }

            try {
                DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS tbl_activities_facility_used_index ON tbl_activities (facility_used)');
            } catch (\Throwable $e) {
                // ignore and continue
            }

            try {
                DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS tbl_activities_service_type_index ON tbl_activities (service_type)');
            } catch (\Throwable $e) {
                // ignore and continue
            }

            try {
                DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS tbl_activities_md_training_index ON tbl_activities (md_training)');
            } catch (\Throwable $e) {
                // ignore and continue
            }

            return;
        }

        // Fallback: attempt to add indexes and ignore duplicate-index errors
        try {
            Schema::table('tbl_activities', function (Blueprint $table) {
                $table->index('activity_at');
            });
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            Schema::table('tbl_activities', function (Blueprint $table) {
                $table->index('facility_used');
            });
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            Schema::table('tbl_activities', function (Blueprint $table) {
                $table->index('service_type');
            });
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            Schema::table('tbl_activities', function (Blueprint $table) {
                $table->index('md_training');
            });
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'pgsql') {
            try {
                DB::statement('DROP INDEX CONCURRENTLY IF EXISTS tbl_activities_activity_at_index');
            } catch (\Throwable $e) {
            }

            try {
                DB::statement('DROP INDEX CONCURRENTLY IF EXISTS tbl_activities_facility_used_index');
            } catch (\Throwable $e) {
            }

            try {
                DB::statement('DROP INDEX CONCURRENTLY IF EXISTS tbl_activities_service_type_index');
            } catch (\Throwable $e) {
            }

            try {
                DB::statement('DROP INDEX CONCURRENTLY IF EXISTS tbl_activities_md_training_index');
            } catch (\Throwable $e) {
            }

            return;
        }

        try {
            Schema::table('tbl_activities', function (Blueprint $table) {
                $table->dropIndex(['activity_at']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            Schema::table('tbl_activities', function (Blueprint $table) {
                $table->dropIndex(['facility_used']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            Schema::table('tbl_activities', function (Blueprint $table) {
                $table->dropIndex(['service_type']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            Schema::table('tbl_activities', function (Blueprint $table) {
                $table->dropIndex(['md_training']);
            });
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
