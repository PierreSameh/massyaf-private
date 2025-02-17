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
        Schema::table('compounds', function (Blueprint $table) {
            $table->json('policies')->nullable();
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->json('policies')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compounds', function (Blueprint $table) {
            $table->dropColumn('policies');
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('policies');
        });
    }
};
