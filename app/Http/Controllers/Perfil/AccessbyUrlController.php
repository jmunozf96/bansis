<?php

namespace App\Http\Controllers\Perfil;

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
        return $this->middleware('AccesoURL');
    }

    public function url($modulo, $idRecurso, $objeto)
    {
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);

        if(view()->exists($modulo . '.' . $idRecurso)){
            return view($modulo . '.' . $idRecurso, [
                'recursos' => $recursos
            ]);
        }

        return redirect('/');

    }
}
