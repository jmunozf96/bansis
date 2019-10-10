<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XASS_InvProductos extends Model
{
    protected $connection = 'xass';
    protected $table = 'SGI_Inv_Productos';
    protected $primaryKey = 'id_fila';

    public function bodega()
    {
        return $this->hasOne('App\XASS_InvBodegas', 'Id_Fila', 'bodegacompra');
    }
}
