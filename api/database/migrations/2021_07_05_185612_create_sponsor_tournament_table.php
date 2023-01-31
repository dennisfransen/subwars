<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorTournamentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("sponsor_tournament", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("sponsor_id");
            $table->unsignedInteger("tournament_id");

            $table->foreign("sponsor_id")
                ->references("id")
                ->on("sponsors");
            $table->foreign("tournament_id")
                ->references("id")
                ->on("tournaments");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("sponsor_tournament");
    }
}
