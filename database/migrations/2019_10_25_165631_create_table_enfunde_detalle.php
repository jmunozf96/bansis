<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEnfundeDetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ENF_DET_ENFUNDE', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo')->nullable();
            $table->integer('idenfunde');
            $table->integer('idseccion');
            $table->integer('cantidad');
            $table->integer('desbunchado')->default(0)->nullable();
            $table->boolean('presente');
            $table->boolean('futuro');
            $table->string('user')->nullable();
            $table->string('pcID')->nullable();
            $table->timestamps();

            $table->foreign('idenfunde')
                ->references('id')
                ->on('ENF_ENFUNDE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ENF_DET_ENFUNDE');
    }
}
