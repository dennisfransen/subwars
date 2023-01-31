<?php

use App\Http\Enums\TournamentEntryLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntryLevelToTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("tournaments", function (Blueprint $table) {
            $table->integer("entry_level")->default(TournamentEntryLevel::NONE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("tournaments", function (Blueprint $table) {
            $table->dropColumn("entry_level");
        });
    }
}
