<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMatchesToFightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename("matches", "fights");
        Schema::rename("match_team", "fight_team");

        Schema::table("fight_team", function (Blueprint $table) {
            $table->renameColumn("match_id", "fight_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename("fights", "matches");
        Schema::rename("fight_team", "match_team");

        Schema::table("match_team", function (Blueprint $table) {
            $table->renameColumn("fight_id", "match_id");
        });
    }
}
