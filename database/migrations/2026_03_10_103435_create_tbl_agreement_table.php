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
        if (Schema::hasTable('tbl_agreement')) {
            return;
        }

        Schema::create('tbl_agreement', function (Blueprint $table) {
            $table->id();
            $table->text('privacy_info')->nullable();
            $table->text('tos_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_agreement');
    }
};
