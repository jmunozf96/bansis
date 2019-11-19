<?php

namespace App\Http\Controllers\Perfil;

use App\Empleado;
use App\Http\Controllers\EgresoController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\XASS_InvBodegas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccessbyUrlController extends Controller
{
    protected $perfil;
    protected $utilidades;

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('AccesoURL');
        $this->perfil = new PerfilController();
        $this->utilidades = new UtilidadesController();
    }

    public function url($modulo, $objeto, $idRecurso)
    {
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);
        Auth::user()->modulo = $modulo;
        Auth::user()->recursoId = $idRecurso;
        Auth::user()->objeto = $objeto;

        $no_existe = array();


        foreach ($recursos as $recurso) {
            if ($recurso->Controlador &&
                trim(strtolower($recurso->objeto)) == trim(strtolower($objeto)) &&
                trim(strtolower($recurso->modulo)) == trim(strtolower($modulo)) &&
                trim(strtolower($recurso->ID)) == trim(strtolower($idRecurso))
            ) {
                return app('App\Http\Controllers\\' . $recurso->Controlador)->index($objeto, $recursos);
            } else {
                array_push($no_existe, true);
            }
        }

        if (count($no_existe) > 0) {
            return redirect('/');
        }

    }
}
