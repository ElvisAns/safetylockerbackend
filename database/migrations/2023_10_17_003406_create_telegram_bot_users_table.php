<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramBotUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_bot_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("username")->require();
            $table->bigInteger('chat_id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegram_bot_users');
    }
}
