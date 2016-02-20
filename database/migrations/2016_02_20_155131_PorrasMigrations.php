<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PorrasMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('porras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->integer('apuesta');
            $table->integer('bote');
            $table->integer('vuelta');
            $table->integer('propietario')->unsigned();
            $table->foreign('propietario')->references('id')->on('users');
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
        Schema::drop('porras');
    }
}
