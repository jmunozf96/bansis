<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEgresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ENF_EGRESOS', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo')->nullable();
            $table->date('fecha');
            $table->string('semana');
            $table->integer('idhacienda');
            $table->integer('idempleado');
            $table->float('total');
            $table->float('saldo');
            $table->binary('status');
            $table->string('user')->nullable();
            $table->string('pcID')->nullable();
            $table->timestamps();

            $table->foreign('idhacienda')
                ->references('id_hac')->on('SIS_HACIENDA');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('egresos');
    }
}
