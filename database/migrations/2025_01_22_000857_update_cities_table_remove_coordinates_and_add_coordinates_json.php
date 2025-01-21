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
        Schema::table('cities', function (Blueprint $table) {
            // Drop the old lat/lng columns
            $table->dropColumn([
                'lat_top_right',
                'lng_top_right',
                'lat_top_left',
                'lng_top_left',
                'lat_bottom_right',
                'lng_bottom_right',
                'lat_bottom_left',
                'lng_bottom_left',
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
        Schema::table('cities', function (Blueprint $table) {
            // Re-add the old columns (if rolling back)
            $table->string('lat_top_right')->nullable();
            $table->string('lng_top_right')->nullable();
            $table->string('lat_top_left')->nullable();
            $table->string('lng_top_left')->nullable();
            $table->string('lat_bottom_right')->nullable();
            $table->string('lng_bottom_right')->nullable();
            $table->string('lat_bottom_left')->nullable();
            $table->string('lng_bottom_left')->nullable();
            
            // Drop the new coordinates column
            $table->dropColumn('coordinates');
        });
    }
};
