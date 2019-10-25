<?php

namespace App\Http\Controllers;

use App\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function Empleados($criterio)
    {
        $empleado = Empleado::selectRaw('trim(NOMBRE_CORTO) as nombre, COD_TRABAJ as codigo')
            ->whereRaw("locate('" . $criterio . "',NOMBRE_CORTO) > 0")
            ->where('ESTADO','A')
            ->get();
        return $empleado;
    }
}
