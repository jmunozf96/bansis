<?php

namespace App\Http\Controllers\Sistema;

use App\Calendario;
use App\XASS_InvBodegas;
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

    public static function getSemana($fecha = null)
    {
        $fecha = is_null($fecha) ? date("d/m/Y", strtotime(date("d-m-Y"))) : $fecha;
        $data = DB::select('exec SP_GETCOLORES_SEMANA ?', array($fecha));
        return $data;
    }

    public static function getColorCodigo($codigo)
    {
        $color = DB::table('SIS_CALENDARIO_DOLE')->select('idcalendar', 'color')
            ->where('idcalendar', $codigo)
            ->first();

        return $color;
    }

    public function getProductos($bodega, $criterio)
    {
        $productos = XASS_InvProductos::selectRaw("id_fila as codigo, RTRIM(nombre) as nombre, unidad, 'Stock: ' + convert(varchar,convert(integer,stock)) as stock_det, stock, bodegacompra")
            ->where('bodegacompra', '=', $bodega)
            ->where('grupo', '4001')
            ->whereRaw("nombre like '%funda%'")
            ->whereRaw("CHARINDEX('" . $criterio . "', nombre) > 0")
            ->with(['bodega' => function ($query) {
                $query->select('Id_Fila', 'Nombre', 'Direccion');
                $query->where('Estado', '=', 1);
            }])
            ->orderBy('stock', 'DESC')->get();
        return $productos;
    }

    public function getcomboProductos($bodega)
    {
        $productos = XASS_InvProductos::selectRaw("id_fila as codigo, RTRIM(nombre) as nombre, unidad, 'Stock: ' + convert(varchar,convert(integer,stock)) as stock_det, stock, bodegacompra")
            ->where('bodegacompra', '=', $bodega)
            ->where('grupo', '4001')
            ->whereIn('id_fila', ['2426', '2428','2446','2448','2813','2833'])
            ->whereRaw("nombre like '%funda%'")
            ->with(['bodega' => function ($query) {
                $query->select('Id_Fila', 'Nombre', 'Direccion');
                $query->where('Estado', '=', 1);
            }])
            ->orderBy('stock', 'DESC')->get();
        return $productos;
    }

    public function Bodegas()
    {
        $bodegas = XASS_InvBodegas::all();
        return $bodegas;
    }

    function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    function unsetValue(array $array, $value, $strict = TRUE)
    {
        if (($key = array_search($value, $array, $strict)) !== FALSE) {
            unset($array[$key]);
        }
        return $array;
    }

    public function comboSemanas()
    {
        $periodo = array();
        $periodo[''] = 'Todas las semanas';

        $c = 1;
        for ($x = 1; $x <= 13; $x++) {
            $semana = array();
            for ($y = 1; $y <= 4; $y++) {
                $semana[$c] = "Semana $c";
                $c++;
            }
            $periodo["Periodo $x"] = $semana;
        }

        return (Object)$periodo;
    }
}
