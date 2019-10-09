<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XASS_InvProductos extends Model
{
    protected $connection = 'xass';
    protected $table = 'SGI_Inv_Productos';
    protected $primaryKey = 'Id_Fila';

    public function bodegas()
    {
        return $this->hasOne('App\XASS_InvBodegas');
    }
}
