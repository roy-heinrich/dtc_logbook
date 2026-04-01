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
        Schema::table('tbl_regusers', function (Blueprint $table) {
            // Add indexes for commonly searched columns
            $table->index('lname_user', 'idx_lname_user');
            $table->index('fname_user', 'idx_fname_user');
            $table->index('sector_user', 'idx_sector_user');
            
            // Add composite index for full name searches
            $table->index(['lname_user', 'fname_user'], 'idx_full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_regusers', function (Blueprint $table) {
            $table->dropIndex('idx_lname_user');
            $table->dropIndex('idx_fname_user');
            $table->dropIndex('idx_sector_user');
            $table->dropIndex('idx_full_name');
        });
    }
};
