<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add functional indexes for case-insensitive searches in PostgreSQL
        DB::statement('CREATE INDEX idx_lname_user_lower ON tbl_regusers (LOWER(lname_user))');
        DB::statement('CREATE INDEX idx_fname_user_lower ON tbl_regusers (LOWER(fname_user))');
        DB::statement('CREATE INDEX idx_mname_user_lower ON tbl_regusers (LOWER(mname_user))');
        DB::statement('CREATE INDEX idx_sector_user_lower ON tbl_regusers (LOWER(sector_user))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_lname_user_lower');
        DB::statement('DROP INDEX IF EXISTS idx_fname_user_lower');
        DB::statement('DROP INDEX IF EXISTS idx_mname_user_lower');
        DB::statement('DROP INDEX IF EXISTS idx_sector_user_lower');
    }
};
