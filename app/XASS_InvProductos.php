<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class XASS_InvProductos extends Model
{
    protected $connection = 'xass';
    protected $table = 'SGI_Inv_Productos';
    protected $primaryKey = 'id_fila';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (Auth::user()->idHacienda == 0 || Auth::user()->idHacienda == 1) {
            $this->connection = 'xass';
        } else {
            $this->connection = 'xass_sofca';
        }
    }

    public function bodega()
    {
        return $this->hasOne('App\XASS_InvBodegas', 'Id_Fila', 'bodegacompra');
    }

    public function changeConnection()
    {
        $this->connection = 'xass_sofca';
    }
}
