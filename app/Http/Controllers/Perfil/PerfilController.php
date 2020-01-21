<?php

namespace App\Http\Controllers\Perfil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function getRecursos($IDusuario)
    {
        $recursos = DB::table('SEG_PERFILES')
            ->join('SEG_RECURSOS as rec', 'rec.ID', '=', 'SEG_PERFILES.RecursoID')
            ->join('SEG_USUARIOS', 'SEG_USUARIOS.ID', '=', 'SEG_PERFILES.id')
            ->select('rec.Nombre', 'rec.Tipo', 'rec.ID', 'rec.PadreID', 'rec.icono', 'rec.modulo', 'rec.objeto', 'rec.Controlador', 'rec.url')
            ->where('SEG_PERFILES.id', '=', $IDusuario)
            ->where('rec.web', '=', 1)
            ->orderBy('rec.NOMBRE', 'asc')
            ->get();

        return $recursos;
    }

}
