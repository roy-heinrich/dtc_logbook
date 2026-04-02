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
            $table->index('service_type', 'tbl_activities_service_type_index');
            $table->index('facility_used', 'tbl_activities_facility_used_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_activities', function (Blueprint $table) {
            $table->dropIndex('tbl_activities_service_type_index');
            $table->dropIndex('tbl_activities_facility_used_index');
        });
    }
};