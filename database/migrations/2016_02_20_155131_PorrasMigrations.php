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
            $table->float('apuesta');
            $table->float('bote');
            $table->integer('vuelta');
            $table->integer('n_jugadores');
            $table->integer('propietario')->unsigned();
            $table->foreign('propietario')->references('id')->on('users');
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin');
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
