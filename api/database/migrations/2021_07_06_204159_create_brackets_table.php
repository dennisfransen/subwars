<?php

use App\Http\Enums\ReleaseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBracketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("brackets", function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->integer("status")->default(ReleaseStatus::COMING_SOON);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("brackets");
    }
}
