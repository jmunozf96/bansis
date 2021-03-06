<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class ENF_EGRESO extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_EGRESOS';
    protected $primaryKey = 'id';
    protected $dateFormat = 'd-m-Y H:i:s';

    public function egresos()
    {
        return $this->hasMany('App\Sisban\Enfunde\ENF_DET_EGRESO', 'id_egreso', 'id');
    }

    public function empleado()
    {
        return $this->hasOne('App\Empleado', 'COD_TRABAJ', 'idempleado');
    }

    public function lotero()
    {
        return $this->hasOne('App\Sisban\Enfunde\ENF_LOTERO', 'idempleado', 'idempleado');
    }

}
