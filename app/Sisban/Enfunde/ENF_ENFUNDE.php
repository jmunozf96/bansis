<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class ENF_ENFUNDE extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_ENFUNDE';
    protected $primaryKey = 'id';
    protected $dateFormat = 'M j Y h:i:s';

    public function detalle()
    {
        return $this->hasMany('App\Sisban\Enfunde\ENF_DET_ENFUNDE', 'idenfunde', 'id');
    }

    public function lotero()
    {
        return $this->hasOne('App\Sisban\Enfunde\ENF_LOTERO', 'id', 'idlotero');
    }

    public function scopeSearch($q)
    {
        return empty(request()->search) ? $q : $q->with(['lotero' => function ($query2) {
            $query2->with(['empleado' => function ($query2) {
                $query2->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
                $query2->orderBy('NOMBRE_CORTO', 'asc');
                $query2->where('NOMBRE_CORTO n', 'like', '%' . request()->search . '%');
            }]);
        }]);
    }
}
