<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SEG_USUARIOS', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->string('CODIGO');
            $table->string('role');
            $table->string('name');
            $table->string('surname');
            $table->string('usuario')->unique();
            $table->string('email')->unique();
            $table->string('password')->unique();
            $table->string('GRUPOID');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('GRUPOID')->references('ID')->on('SEG_GRUPOS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('SEG_USUARIOS');
    }
}
