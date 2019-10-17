<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class ENF_DET_EGRESO extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_DET_EGRESOS';
    protected $primaryKey = 'id';
    protected $dateFormat = 'M j Y h:i:s';

    public function get_egreso()
    {
        return $this->belongsTo('App\Sisban\Enfunde\ENF_EGRESO', 'id', 'id_egreso');
    }

    public function get_material()
    {
        return $this->hasOne('App\XASS_InvProductos', 'id_fila', 'idmaterial');
    }

    public function get_bodega()
    {
        return $this->hasOne('App\XASS_InvBodegas', 'Id_Fila', 'idbodega');
    }
}
