<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrioritizeByEntryLevelToTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("tournaments", function (Blueprint $table) {
            $table->boolean("prioritize_by_entry_level")->default(false);
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
            $table->dropColumn("prioritize_by_entry_level");
        });
    }
}
