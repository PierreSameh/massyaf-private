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
            $table->dropColumn([
                'lat_top_right', 
                'lng_top_right', 
                'lat_top_left', 
                'lng_top_left', 
                'lat_bottom_right', 
                'lng_bottom_right', 
                'lat_bottom_left', 
                'lng_bottom_left'
            ]);

            // Add new address, lat, and lng columns
            $table->string('address')->nullable();
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compounds', function (Blueprint $table) {
            // Remove the new columns
            $table->dropColumn(['address', 'lat', 'lng']);

            // Re-add the dropped latitude and longitude columns
            $table->string('lat_top_right')->nullable();
            $table->string('lng_top_right')->nullable();
            $table->string('lat_top_left')->nullable();
            $table->string('lng_top_left')->nullable();
            $table->string('lat_bottom_right')->nullable();
            $table->string('lng_bottom_right')->nullable();
            $table->string('lat_bottom_left')->nullable();
            $table->string('lng_bottom_left')->nullable();
        });
    }
};
