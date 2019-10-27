<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\Sisban\Enfunde\ENF_LOTERO;
use App\Sisban\Hacienda\SIS_LOTE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class EnfundeController extends Controller
{
    protected $perfil;
    protected $utilidades;
    protected $recursos;

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('AccesoURL',
            ['except' => ['getLotero', 'Loteros']]);
        date_default_timezone_set('America/Guayaquil');
        $this->perfil = new PerfilController();
        $this->utilidades = new UtilidadesController();
    }

    public function index($objeto, $recursos)
    {
        $this->recursos = $recursos;
        if (view()->exists('enfunde' . '.' . $objeto)) {
            return view('enfunde' . '.' . $objeto, [
                'recursos' => $this->recursos,
                'semana' => $this->utilidades->getSemana()
            ]);
        }
    }

    public function form()
    {
        $current_params = Route::current()->parameters();

        $this->recursos = $this->perfil->getRecursos(Auth::user()->ID);
        Auth::user()->modulo = $current_params['modulo'];
        Auth::user()->objeto = $current_params['objeto'];
        Auth::user()->recursoId = $current_params['idRecurso'];

        $hacienda = Auth::user()->idHacienda = 0 ? 1 : Auth::user()->idHacienda;
        $data = [
            'recursos' => $this->recursos,
            'semana' => $this->utilidades->getSemana(),
            'loteros' => $this->Loteros(1),
        ];


        return view('enfunde.enf_enfunde_registro', $data);
    }

    public function getLotero($idlotero, $semana)
    {
        $lotero = ENF_LOTERO::select('id', 'idempleado')
            ->where('id', $idlotero)
            ->with(['seccion' => function ($query) {
                $query->whereHas('lote', function ($query2) {
                    $query2->where('estado', 1);
                });
            }, 'seccion.lote'])
            ->with(['empleado' => function ($query2) {
                $query2->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
                $query2->orderBy('NOMBRE_CORTO','asc');
            }])
            ->with(['fundas' => function ($query3) use ($semana) {
                $query3->select('id', 'semana', 'idempleado', 'total');
                $query3->where('semana', $semana);
                $query3->with(['egresos' => function ($query7) {
                    $query7->select('id', 'id_egreso', 'fecha', 'cantidad');
                    $query7->where('reemplazo', false);
                }]);
            }])
            ->with(['fundas_reemplazo' => function ($query4) use ($semana) {
                $query4->select('id', 'id_egreso', 'cantidad', 'fecha', 'presente', 'futuro', 'idempleado');
                $query4->with(['get_egreso' => function ($query5) use ($semana) {
                    $query5->select('id', 'semana', 'idempleado');
                    $query5->where('semana', $semana);
                    $query5->with(['empleado' => function ($query6) {
                        $query6->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
                    }]);
                }]);
            }])
            ->get();
        return $lotero;
    }

    public function Loteros($hacienda)
    {
        $empleado = ENF_LOTERO::selectRaw('id, idempleado')
            ->with(['empleado' => function ($query) {
                $query->selectRaw('trim(NOMBRE_CORTO) as nombre, COD_TRABAJ');
                $query->where('ESTADO', 'A');
            }])
            ->where('idhacienda', $hacienda)
            ->get();

        return $empleado;
    }

}
