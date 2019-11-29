<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class XASS_InvBodegas extends Model
{
    protected $connection = 'xass';
    protected $table = 'SGI_Inv_Bodegas';
    protected $primaryKey = 'Id_Fila';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (Auth::user()->idHacienda == 0 || Auth::user()->idHacienda == 1) {
            $this->connection = 'xass';
        } else {
            $this->connection = 'xass_sofca';
        }
    }

    public function productos()
    {
        return $this->hasMany('App\XASS_InvProductos');
    }
}
