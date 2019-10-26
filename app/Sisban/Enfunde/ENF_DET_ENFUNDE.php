<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class ENF_DET_ENFUNDE extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_DET_ENFUNDE';
    protected $id = 'id';
    protected $dateFormat = 'M j Y h:i:s';

    public function seccion(){
        return $this->hasOne('App\Sisban\Enfunde\ENF_SEC_LOTERO','idseccion','id');
    }
}
