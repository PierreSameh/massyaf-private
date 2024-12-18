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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lat_top_right')->nullable();
            $table->string('lng_top_right')->nullable();
            $table->string('lat_top_left')->nullable();
            $table->string('lng_top_left')->nullable();
            $table->string('lat_bottom_right')->nullable();
            $table->string('lng_bottom_right')->nullable();
            $table->string('lat_bottom_left')->nullable();
            $table->string('lng_bottom_left')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
