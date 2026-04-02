<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_activities', function (Blueprint $table) {
            $table->index(['service_type', 'activity_at'], 'tbl_activities_service_type_activity_at_index');
            $table->index(['facility_used', 'activity_at'], 'tbl_activities_facility_used_activity_at_index');
            $table->index(['md_training', 'activity_at'], 'tbl_activities_md_training_activity_at_index');
        });

        Schema::table('login_logs', function (Blueprint $table) {
            $table->index(['user_type', 'login_at'], 'login_logs_user_type_login_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_activities', function (Blueprint $table) {
            $table->dropIndex('tbl_activities_service_type_activity_at_index');
            $table->dropIndex('tbl_activities_facility_used_activity_at_index');
            $table->dropIndex('tbl_activities_md_training_activity_at_index');
        });

        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropIndex('login_logs_user_type_login_at_index');
        });
    }
};
