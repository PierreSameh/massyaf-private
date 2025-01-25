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
            // Drop the old lat/lng columns
            $table->dropColumn([
                'lat',
                'lng',
            ]);
            
            // Add a new JSON column for coordinates
            $table->json('coordinates')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compounds', function (Blueprint $table) {
            // Re-add the old columns (if rolling back)
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            
            // Drop the new coordinates column
            $table->dropColumn('coordinates');
        });
    }
};
