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
            $table->index('activity_at', 'tbl_activities_activity_at_index');
            $table->index(['user_id', 'activity_at'], 'tbl_activities_user_activity_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_activities', function (Blueprint $table) {
            $table->dropIndex('tbl_activities_activity_at_index');
            $table->dropIndex('tbl_activities_user_activity_at_index');
        });
    }
};