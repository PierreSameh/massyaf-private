<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'owner_id', 'muted_for_owner', 'muted_for_user']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('sender_type');
        });
    }

    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->boolean('muted_for_owner')->default(false);
            $table->boolean('muted_for_user')->default(false);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender_type');
        });
    }
};
