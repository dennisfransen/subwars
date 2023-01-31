<?php

use App\Http\Enums\SupportTicketPriority;
use App\Http\Enums\SupportTicketType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("support_tickets", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("sender_id")->nullable();
            $table->string("email")->nullable();
            $table->unsignedInteger("responder_id")->nullable();
            $table->dateTime("read_at")->nullable();
            $table->text("description");
            $table->unsignedInteger("type")->default(SupportTicketType::UNSPECIFIED);
            $table->unsignedInteger("priority")->default(SupportTicketPriority::GUEST);
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
        Schema::dropIfExists("support_tickets");
    }
}
