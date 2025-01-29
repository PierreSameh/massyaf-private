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
            // Step 1: Add temporary JSON columns
            $table->json('name_json')->nullable();
            $table->json('description_json')->nullable();
            $table->json('features_json')->nullable();
        });

        // Step 2: Convert existing non-JSON data to JSON format
        DB::statement('UPDATE compounds SET name_json = JSON_OBJECT("en", name) WHERE name IS NOT NULL');
        DB::statement('UPDATE compounds SET description_json = JSON_OBJECT("en", description) WHERE description IS NOT NULL');
        DB::statement('UPDATE compounds SET features_json = JSON_OBJECT("en", features) WHERE features IS NOT NULL');

        Schema::table('compounds', function (Blueprint $table) {
            // Step 3: Drop old columns
            $table->dropColumn(['name', 'description', 'features']);
        });

        Schema::table('compounds', function (Blueprint $table) {
            // Step 4: Rename the new JSON columns to match original names
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('description_json', 'description');
            $table->renameColumn('features_json', 'features');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compounds', function (Blueprint $table) {
            // Step 1: Add back original columns
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('features')->nullable();
        });

        // Step 2: Convert JSON data back to string
        DB::statement('UPDATE compounds SET name = JSON_UNQUOTE(JSON_EXTRACT(name, "$.en")) WHERE name IS NOT NULL');
        DB::statement('UPDATE compounds SET description = JSON_UNQUOTE(JSON_EXTRACT(description, "$.en")) WHERE description IS NOT NULL');
        DB::statement('UPDATE compounds SET features = JSON_UNQUOTE(JSON_EXTRACT(features, "$.en")) WHERE features IS NOT NULL');

        Schema::table('compounds', function (Blueprint $table) {
            // Step 3: Drop JSON columns
            $table->dropColumn(['name', 'description', 'features']);
        });
    }
};
