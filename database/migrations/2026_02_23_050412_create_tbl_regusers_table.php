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
        Schema::create('tbl_regusers', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('fname_user', 50)->nullable();
            $table->string('lname_user', 50);
            $table->string('mname_user', 50);
            $table->string('suffix_user', 2)->nullable();
            $table->date('birthdate')->nullable();
            $table->char('sex_user', 1);
            $table->string('sector_user', 100);
            $table->string('number_user', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_regusers');
    }
};
