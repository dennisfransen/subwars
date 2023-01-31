<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoCastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("co_casters", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("tournament_id");
            $table->unsignedInteger("user_id");

            $table->foreign("tournament_id")
                ->references("id")
                ->on("tournaments");
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
        Schema::dropIfExists("co_casters");
    }
}
