<?php

namespace App\Http\Controllers\Perfil;

use App\XASS_InvBodegas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccessbyUrlController extends Controller
{
    protected $perfil;

    function __construct()
    {
        $this->perfil = new PerfilController();
        $this->middleware('auth');
        $this->middleware('AccesoURL');
    }

    public function url($modulo, $idRecurso, $objeto)
    {
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);

        if (view()->exists($modulo . '.' . $idRecurso)) {
            return view($modulo . '.' . $idRecurso, [
                'recursos' => $recursos,
                'bodegas' => $this->Bodegas()
            ]);
        }

        return redirect('/');

    }

    public function Bodegas()
    {
        $bodegas = XASS_InvBodegas::all();
        return $bodegas;
    }
}
