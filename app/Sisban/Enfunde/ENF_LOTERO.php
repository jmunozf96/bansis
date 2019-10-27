<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class ENF_LOTERO extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_LOTEROS';
    protected $primaryKey = 'id';
    protected $dateFormat = 'M j Y h:i:s';

    public function empleado()
    {
        return $this->hasOne('App\Empleado', 'COD_TRABAJ', 'idempleado');
    }

    public function seccion()
    {
        return $this->hasMany('App\Sisban\Enfunde\ENF_SEC_LOTERO', 'idlotero', 'id');
    }

    public function fundas(){
        return $this->hasOne('App\Sisban\Enfunde\ENF_EGRESO','idempleado','idempleado');
    }

    public function fundas_reemplazo(){
        return $this->hasMany('App\Sisban\Enfunde\ENF_DET_EGRESO','idempleado','idempleado');
    }
}
