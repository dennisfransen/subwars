<?php

use App\Http\Enums\TournamentUserState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("tournament_user", function (Blueprint $table) {
            $table->primary(["tournament_id", "user_id"], "id");
            $table->unsignedInteger("tournament_id");
            $table->unsignedInteger("user_id");
            $table->integer("state")->default(TournamentUserState::REGISTERED);
            $table->unsignedInteger("order")->default(1);
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
        Schema::dropIfExists("tournament_user");
    }
}
