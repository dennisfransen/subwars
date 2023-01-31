<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwitchStateToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->string("twitch_state")->nullable();
            $table->text("twitch_scope")->nullable();
            $table->string("twitch_login")->nullable();
            $table->string("twitch_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->removeColumn("twitch_scope");
            $table->removeColumn("twitch_state");
            $table->removeColumn("twitch_login");
            $table->removeColumn("twitch_id");
        });
    }
}
