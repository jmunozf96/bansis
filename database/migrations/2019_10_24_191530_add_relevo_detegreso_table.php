<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelevoDetegresoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ENF_DET_EGRESOS', function (Blueprint $table) {
            $table->boolean('reemplazo')->nullable();
            $table->integer('idempleado')->nullable()->after('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ENF_DET_EGRESOS', function (Blueprint $table) {
            //
        });
    }
}
