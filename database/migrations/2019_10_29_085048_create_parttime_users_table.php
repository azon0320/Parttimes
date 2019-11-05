<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParttimeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parttime_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string("phone");
            $table->string("uid");
            $table->integer("credit")->default(500);
            $table->string("password");
            $table->string("nickname");
            $table->string("token", 64)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parttime_users');
    }
}
