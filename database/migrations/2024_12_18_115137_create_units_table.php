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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['unit', 'hotel']);
            $table->foreignId('unit_type_id')->constrained('types');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('compound_id')->nullable()->constrained('compounds');
            $table->foreignId('hotel_id')->nullable()->constrained('hotels');
            $table->string('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('unit_number');
            $table->integer('floors_count')->default(1);
            $table->boolean('elevator')->default(false);
            $table->integer('area');
            $table->decimal('distance_unit_beach', 8, 2)->nullable();
            $table->enum('beach_unit_transportation', ['car', 'walking']);
            $table->decimal('distance_unit_pool', 8, 2)->nullable();
            $table->enum('pool_unit_transportation', ['car', 'walking']);
            $table->integer('room_count');
            $table->integer('toilet_count');
            $table->text('description')->nullable();
            $table->text('reservation_roles')->nullable();
            $table->enum('reservation_type', ['direct', 'request']);
            $table->decimal('price', 8, 2);
            $table->decimal('insurance_amount', 8, 2);
            $table->integer('max_individuals');
            $table->boolean('youth_only')->default(false);
            $table->integer('min_reservation_days')->nullable();
            $table->decimal('deposit', 8, 2);
            $table->decimal('upon_arival_price', 8, 2);
            $table->boolean('weekend_prices')->default(false);
            $table->integer('min_weekend_period')->nullable();
            $table->decimal('weekend_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
