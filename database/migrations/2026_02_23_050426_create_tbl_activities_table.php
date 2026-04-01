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
        Schema::create('tbl_activities', function (Blueprint $table) {
            $table->id('activity_id');
            $table->unsignedBigInteger('user_id');
            $table->string('facility_used', 255);
            $table->string('service_type', 255);
            $table->date('activity_date')->nullable();
            $table->time('activity_time')->nullable();
            
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('tbl_regusers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_activities');
    }
};
