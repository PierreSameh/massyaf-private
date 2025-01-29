<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            // Step 1: Add temporary JSON columns
            $table->json('name_json')->nullable();
            $table->json('description_json')->nullable();
            $table->json('features_json')->nullable();
        });

        // Step 2: Convert existing non-JSON data to JSON format
        DB::statement('UPDATE cities SET name_json = JSON_OBJECT("en", name) WHERE name IS NOT NULL');
        DB::statement('UPDATE cities SET description_json = JSON_OBJECT("en", description) WHERE description IS NOT NULL');
        DB::statement('UPDATE cities SET features_json = JSON_OBJECT("en", features) WHERE features IS NOT NULL');

        Schema::table('cities', function (Blueprint $table) {
            // Step 3: Drop old columns
            $table->dropColumn(['name', 'description', 'features']);
        });

        Schema::table('cities', function (Blueprint $table) {
            // Step 4: Rename the new JSON columns to match original names
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('description_json', 'description');
            $table->renameColumn('features_json', 'features');
        });
    }

    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            // Step 1: Add back original columns
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('features')->nullable();
        });

        // Step 2: Convert JSON data back to string
        DB::statement('UPDATE cities SET name = JSON_UNQUOTE(JSON_EXTRACT(name, "$.en")) WHERE name IS NOT NULL');
        DB::statement('UPDATE cities SET description = JSON_UNQUOTE(JSON_EXTRACT(description, "$.en")) WHERE description IS NOT NULL');
        DB::statement('UPDATE cities SET features = JSON_UNQUOTE(JSON_EXTRACT(features, "$.en")) WHERE features IS NOT NULL');

        Schema::table('cities', function (Blueprint $table) {
            // Step 3: Drop JSON columns
            $table->dropColumn(['name', 'description', 'features']);
        });
    }
};
