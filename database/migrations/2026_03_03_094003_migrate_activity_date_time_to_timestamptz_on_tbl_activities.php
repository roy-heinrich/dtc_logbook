<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new activity_at column
        Schema::table('tbl_activities', function (Blueprint $table) {
            $table->timestamp('activity_at')->nullable()->after('service_type');
        });

        // Migrate existing data: combine activity_date and activity_time into activity_at
        DB::statement("
            UPDATE tbl_activities 
            SET activity_at = CASE 
                WHEN activity_date IS NOT NULL AND activity_time IS NOT NULL 
                    THEN CONCAT(activity_date, ' ', activity_time)
                WHEN activity_date IS NOT NULL 
                    THEN CONCAT(activity_date, ' 00:00:00')
                ELSE NULL
            END
        ");

        // Drop the old columns
        Schema::table('tbl_activities', function (Blueprint $table) {
            $table->dropColumn(['activity_date', 'activity_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the old columns
        Schema::table('tbl_activities', function (Blueprint $table) {
            $table->date('activity_date')->nullable()->after('service_type');
            $table->time('activity_time')->nullable()->after('activity_date');
        });

        // Migrate data back: split activity_at into activity_date and activity_time
        DB::statement("
            UPDATE tbl_activities 
            SET activity_date = DATE(activity_at),
                activity_time = TIME(activity_at)
            WHERE activity_at IS NOT NULL
        ");

        // Drop the activity_at column
        Schema::table('tbl_activities', function (Blueprint $table) {
            $table->dropColumn('activity_at');
        });
    }
};
