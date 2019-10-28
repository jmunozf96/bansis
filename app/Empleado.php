<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rh_mtrab';

    public function lotero(){
        return $this->hasOne('App\Sisban\Enfunde\ENF_LOTERO','idempleado','COD_TRABAJ');
    }
}
