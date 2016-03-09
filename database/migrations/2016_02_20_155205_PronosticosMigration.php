<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PronosticosMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pronosticos', function (Blueprint $table) {
            $table->integer('partido_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('porra_id')->unsigned();
            $table->foreign('porra_id')->references('id')->on('porras');
            $table->integer('goles_local')->default(null);
            $table->integer('goles_visitante')->default(null);
            $table->boolean('cerrado')->default(false);
            $table->primary(array('partido_id','porra_id', 'user_id'));
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
        Schema::drop('pronosticos');
    }
}
