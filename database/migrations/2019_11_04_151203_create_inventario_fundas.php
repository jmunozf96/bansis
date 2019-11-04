<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventarioFundas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('INV_LOT_FUND', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('semana');
            $table->integer('idlotero');
            $table->integer('idmaterial');
            $table->integer('saldo');
            $table->boolean('presente');
            $table->boolean('futuro');
            $table->boolean('status');
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
        Schema::dropIfExists('INV_LOT_FUND');
    }
}
