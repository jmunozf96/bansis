<?php

namespace App\Sisban\Hacienda;

use Illuminate\Database\Eloquent\Model;

class SIS_LOTE extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SIS_LOTES';
    protected $primaryKey = 'id';
    protected $dateFormat = 'M j Y h:i:s';

    public function seccion(){
        return $this->hasOne('App\Sisban\Enfunde\ENF_SEC_LOTERO','idlote','id');
    }
}
