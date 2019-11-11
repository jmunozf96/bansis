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
            $table->integer('idlotero');
            $table->integer('semana');
            $table->integer('idmaterial');
            $table->integer('saldo_inicial')->nullable();
            $table->integer('entrada');
            $table->integer('salida')->nullable();
            $table->integer('saldo')->nullable()->default(0);
            $table->boolean('status')->nullable()->default(false);
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
