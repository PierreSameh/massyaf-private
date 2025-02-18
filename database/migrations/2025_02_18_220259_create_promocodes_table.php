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
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('promocode')->unique();
            $table->json('description')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('amount_total', 10, 2)->nullable();
            $table->decimal('amount_night', 10, 2)->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promocodes');
    }
};
