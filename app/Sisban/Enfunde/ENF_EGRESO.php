<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class ENF_EGRESO extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_EGRESOS';
    protected $primaryKey = 'id';

    public function get_det_egresos()
    {
        return $this->belongsToMany('App\Sisban\Enfunde\ENF_DET_EGRESO');
    }

    public function get_empleado()
    {
        return $this->hasOne('App\Empleado', 'COD_TRABAJ', 'idempleado');
    }
}
