<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetegresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ENF_DET_EGRESOS', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo')->nullable();
            $table->integer('id_egreso');
            $table->date('fecha');
            $table->integer('idbodega')->nullable();
            $table->integer('idmaterial');
            $table->float('cantidad');
            $table->binary('presente')->nullable();
            $table->binary('futuro')->nullable();
            $table->binary('status')->nullable();
            $table->timestamps();

            $table->foreign('id_egreso')
                ->references('id')->on('ENF_EGRESOS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detegresos');
    }
}
