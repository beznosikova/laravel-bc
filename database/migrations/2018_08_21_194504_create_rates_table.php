<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bc_id_from')->unsigned();
            $table->foreign('bc_id_from')->references('bc_id')->on('currencies');
            $table->integer('bc_id_to')->unsigned();
            $table->foreign('bc_id_to')->references('bc_id')->on('currencies');
            $table->integer('bc_id_exchange')->unsigned();
            $table->foreign('bc_id_exchange')->references('bc_id')->on('exchanges');
            $table->string('rate_from');
            $table->string('rate_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rates');
    }
}
