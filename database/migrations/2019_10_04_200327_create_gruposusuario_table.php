<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGruposusuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SEG_GRUPOS', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->string('CODIGO');
            $table->string('DESCRIPCION');
            $table->binary('estado');
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
        Schema::dropIfExists('SEG_GRUPOS');
    }
}
