<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    protected $perfil;

    public function __construct()
    {
        $this->perfil = new PerfilController();
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);
        return view('home', [
            'recursos' => $recursos
        ]);
    }
}
