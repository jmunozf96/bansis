<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class ENF_ENFUNDE extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_ENFUNDE';
    protected $primaryKey = 'id';
    protected $dateFormat = 'M j Y h:i:s';

    public function detalle(){
        return $this->hasMany('App\Sisban\Enfunde\ENF_DET_ENFUNDE', 'idenfunde','id');
    }

    public function lotero(){
        return $this->hasOne('App\Sisban\Enfunde\ENF_LOTERO', 'idlotero', 'id');
    }
}
