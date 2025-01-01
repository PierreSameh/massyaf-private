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
        Schema::table('chats', function (Blueprint $table) {
            $table->boolean('muted_for_owner')->default(false)->after('owner_id');
            $table->boolean('muted_for_user')->default(false)->after('muted_for_owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('muted_for_owner');
            $table->dropColumn('muted_for_user');
        });
    }
};
