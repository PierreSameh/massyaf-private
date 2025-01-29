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
        Schema::table('hotels', function (Blueprint $table) {
            // Step 1: Add temporary JSON columns
            $table->json('name_json')->nullable();
            $table->json('description_json')->nullable();
            $table->json('features_json')->nullable();
            $table->json('details_json')->nullable();
        });

        // Step 2: Convert existing non-JSON data to JSON format
        DB::statement('UPDATE hotels SET name_json = JSON_OBJECT("en", name) WHERE name IS NOT NULL');
        DB::statement('UPDATE hotels SET description_json = JSON_OBJECT("en", description) WHERE description IS NOT NULL');
        DB::statement('UPDATE hotels SET features_json = JSON_OBJECT("en", features) WHERE features IS NOT NULL');
        DB::statement('UPDATE hotels SET details_json = JSON_OBJECT("en", details) WHERE details IS NOT NULL');

        Schema::table('hotels', function (Blueprint $table) {
            // Step 3: Drop old columns
            $table->dropColumn(['name', 'description', 'features', 'details']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            // Step 4: Rename the new JSON columns to match original names
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('description_json', 'description');
            $table->renameColumn('features_json', 'features');
            $table->renameColumn('details_json', 'details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            // Step 1: Add back original columns
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('features')->nullable();
            $table->text('details')->nullable();
        });

        // Step 2: Convert JSON data back to string
        DB::statement('UPDATE hotels SET name = JSON_UNQUOTE(JSON_EXTRACT(name, "$.en")) WHERE name IS NOT NULL');
        DB::statement('UPDATE hotels SET description = JSON_UNQUOTE(JSON_EXTRACT(description, "$.en")) WHERE description IS NOT NULL');
        DB::statement('UPDATE hotels SET features = JSON_UNQUOTE(JSON_EXTRACT(features, "$.en")) WHERE features IS NOT NULL');
        DB::statement('UPDATE hotels SET details = JSON_UNQUOTE(JSON_EXTRACT(details, "$.en")) WHERE details IS NOT NULL');

        Schema::table('hotels', function (Blueprint $table) {
            // Step 3: Drop JSON columns
            $table->dropColumn(['name', 'description', 'features', 'details']);
        });
    }
};
