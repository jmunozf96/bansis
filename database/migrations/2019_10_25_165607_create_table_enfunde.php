<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEnfunde extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ENF_ENFUNDE', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo')->nullable();
            $table->integer('idhacienda');
            $table->date('fecha');
            $table->integer('semana');
            $table->integer('periodo');
            $table->integer('cinta_pre');
            $table->integer('cinta_fut');
            $table->integer('idlotero');
            $table->integer('total_pre');
            $table->integer('total_fut');
            $table->integer('chapeo')->nullable();
            $table->integer('count')->default(0);
            $table->boolean('status');
            $table->string('user')->nullable();
            $table->string('pcID')->nullable();
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
        Schema::dropIfExists('ENF_ENFUNDE');
    }
}
