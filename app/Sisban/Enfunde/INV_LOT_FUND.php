<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class INV_LOT_FUND extends Model
{
    protected $table = 'INV_LOT_FUND';
    protected $connection = 'sqlsrv';
    protected $dateFormat = 'M j Y h:i:s';

    public function material()
    {
        return $this->hasOne('App\XASS_InvProductos', 'id_fila', 'idmaterial');
    }
}
