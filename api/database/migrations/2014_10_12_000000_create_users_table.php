<?php

use App\Http\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->integer("type")->default(UserType::GUEST);
            $table->string("username")->nullable()->unique();
            $table->string("password")->nullable();
            $table->integer("esportal_elo")->nullable();
            $table->string("esportal_username")->nullable();
            $table->boolean("streamer")->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists("users");
    }
}
