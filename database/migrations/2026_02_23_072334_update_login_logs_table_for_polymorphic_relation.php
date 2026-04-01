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
        Schema::table('login_logs', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['user_id']);
            
            // Add user_type column for polymorphic relation
            $table->string('user_type')->after('login_log_id')->default('App\\Models\\User');
            
            // Modify user_id to remove constraint (we'll use polymorphic relation)
            $table->unsignedBigInteger('user_id')->change();
            
            // Add index for polymorphic relation
            $table->index(['user_type', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropIndex(['user_type', 'user_id']);
            $table->dropColumn('user_type');
            $table->foreignId('user_id')->change()->constrained()->cascadeOnDelete();
        });
    }
};
