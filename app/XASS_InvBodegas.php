<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XASS_InvBodegas extends Model
{
    protected $connection = 'xass';
    protected $table = 'SGI_Inv_Bodegas';
    protected $primaryKey = 'Id_Fila';

    public function productos()
    {
        return $this->hasMany('App\XASS_InvProductos');
    }
}
