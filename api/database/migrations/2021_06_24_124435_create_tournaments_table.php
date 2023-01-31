<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("tournaments", function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("bracket_id")->nullable();
            $table->text("description")->nullable();
            $table->text("rules")->nullable();
            $table->integer("max_teams")->default(-1);
            $table->integer("min_elo")->default(-1);
            $table->integer("max_elo")->default(-1);
            $table->dateTime("visible_at")->nullable();
            $table->dateTime("live_at")->nullable();
            $table->dateTime("registration_open_at")->nullable();
            $table->dateTime("check_in_open_at")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("tournaments");
    }
}
