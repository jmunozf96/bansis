<?php

namespace App\Http\Controllers\Perfil;

use App\Empleado;
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

    public function url($modulo, $idRecurso, $objeto)
    {
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);

        if (view()->exists($modulo . '.' . $idRecurso)) {
            return view($modulo . '.' . $idRecurso, [
                'recursos' => $recursos,
                'semana' => $this->utilidades->getSemana(),
                'bodegas' => $this->utilidades->Bodegas()
            ]);
        }

        return redirect('/');

    }
}
