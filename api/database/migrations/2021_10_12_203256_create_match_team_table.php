<?php

use App\Http\Enums\FightTeamResult;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("match_team", function (Blueprint $table) {
            $table->primary(["match_id", "team_id"], "id");
            $table->unsignedInteger("match_id");
            $table->unsignedInteger("team_id");
            $table->unsignedInteger("result")->default(FightTeamResult::UNDECIDED);
            $table->integer("score")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("match_team");
    }
}
