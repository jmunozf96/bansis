<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusSeccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ENF_SEC_LOTEROS', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(1);
        });

        $sql = "UPDATE ENF_SEC_LOTEROS SET status = 1";
        //add filtering conditions if you don't want ALL records updated
        DB::connection('sqlsrv')->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ENF_SEC_LOTEROS', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
