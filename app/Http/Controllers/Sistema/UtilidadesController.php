<?php

namespace App\Http\Controllers\Sistema;

use App\Calendario;
use App\XASS_InvProductos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UtilidadesController extends Controller
{
    function __construct()
    {
        //$this->middleware('auth');
    }

    public function getSemana($fecha = null)
    {
        $fecha = is_null($fecha) ? date("d/m/Y", strtotime(date("d-m-Y"))) : $fecha;
        $data = DB::select('exec SP_GETCOLORES_SEMANA ?', array($fecha));
        return $data;
    }

    public function getProductos($bodega, $criterio)
    {
        $productos = XASS_InvProductos::selectRaw('id_fila as codigo, RTRIM(nombre) as nombre, unidad, stock, bodegacompra')
            ->where('bodegacompra', '=', $bodega)
            ->whereRaw("CHARINDEX('" . $criterio . "', nombre) > 0")
            ->with(['bodega' => function ($query) {
                $query->select('Id_Fila', 'Nombre', 'Direccion');
                $query->where('Estado', '=', 1);
            }])->get();
        return $productos;
    }
}
