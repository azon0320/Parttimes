<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParttimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parttimes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string("title");
            $table->timestamp("timestart");
            $table->timestamp("timeend");
            $table->string("location");
            $table->timestamp("deadline");
            $table->string("detail");
            $table->string("img");
            $table->integer("cancelled");
            $table->integer("limited");
            $table->unsignedBigInteger("creator_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parttimes');
    }
}
