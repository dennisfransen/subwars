<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("matches", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("tournament_id");
            $table->unsignedInteger("child_id")->nullable();
            $table->unsignedInteger("round")->default(1);
            $table->unsignedInteger("number")->default(1);
            $table->timestamps();

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
        Schema::dropIfExists("matches");
    }
}
