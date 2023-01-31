<?php

use App\Http\Enums\CasterRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToCoCastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("co_casters", function (Blueprint $table) {
            $table->integer("role")->default(CasterRole::CO_CASTER);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("co_casters", function (Blueprint $table) {
            $table->dropColumn("role");
        });
    }
}
