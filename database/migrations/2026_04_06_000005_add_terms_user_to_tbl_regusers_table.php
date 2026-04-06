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
        if (!Schema::hasTable('tbl_regusers') || Schema::hasColumn('tbl_regusers', 'terms_user')) {
            return;
        }

        Schema::table('tbl_regusers', function (Blueprint $table) {
            $table->text('terms_user')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('tbl_regusers') || !Schema::hasColumn('tbl_regusers', 'terms_user')) {
            return;
        }

        Schema::table('tbl_regusers', function (Blueprint $table) {
            $table->dropColumn('terms_user');
        });
    }
};
