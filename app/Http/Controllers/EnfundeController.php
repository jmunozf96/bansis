<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\Sisban\Enfunde\ENF_DET_ENFUNDE;
use App\Sisban\Enfunde\ENF_ENFUNDE;
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
            ['except' => ['getLotero', 'Loteros', 'save']]);
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

        $hacienda = Auth::user()->idHacienda == 0 ? 1 : Auth::user()->idHacienda;
        $data = [
            'recursos' => $this->recursos,
            'semana' => $this->utilidades->getSemana(),
            'loteros' => $this->Loteros($hacienda),
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
                $query2->orderBy('NOMBRE_CORTO', 'asc');
            }])
            ->with(['fundas' => function ($query3) use ($semana) {
                $query3->select('id', 'semana', 'idempleado', 'total', 'status');
                $query3->where('semana', $semana);
                $query3->with(['egresos' => function ($query7) {
                    $query7->select('id', 'id_egreso', 'fecha', 'cantidad', 'reemplazo', 'idempleado');
                    $query7->with(['nom_reemplazo' => function ($query10) {
                        $query10->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
                        $query10->orderBy('NOMBRE_CORTO', 'asc');
                    }]);
                }]);
            }])
            ->with(['enfunde' => function ($query9) use ($semana) {
                $query9->select('id', 'idlotero', 'semana', 'total_pre', 'total_fut', 'count', 'status');
                $query9->where('semana', $semana);
                $query9->with(['detalle' => function ($query10) {
                    $query10->select('id', 'idenfunde', 'idseccion', 'cantidad', 'desbunchado', 'presente', 'futuro');
                }]);
            }])
            ->
            get();
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

    public function save(Request $request)
    {
        $resp = false;
        $edit = false;

        if ($request->presente && !$request->edicion) {
            $enfunde = new ENF_ENFUNDE();
            $enfunde->idhacienda = $request->idhacienda == '343' ? 1 : 2;
            $enfunde->fecha = $request->fecha;
            $enfunde->semana = $request->semana;
            $enfunde->periodo = $request->periodo;
            $enfunde->cinta_pre = $this->utilidades->getSemana()[0]->cod_color;
            $enfunde->cinta_fut = $this->utilidades->getSemana()[1]->cod_color;
            $enfunde->idlotero = $request->lotero;
            $enfunde->total_pre = $request->total_pre;
            $enfunde->total_fut = $request->total_fut;
            $enfunde->chapeo = $request->desbunchado;
            $enfunde->count = 1;
            $enfunde->status = 1;

            $resp = $enfunde->save();
        } else {
            $enfunde = ENF_ENFUNDE::select('id')
                ->where('idlotero', $request->lotero)
                ->where('semana', $request->semana)->first();

            if ($enfunde) {
                $resp = true;
                $edit = $request->edicion ? true : false;
            }
        }

        $totaliza_presente = 0;
        $totaliza_futuro = 0;

        if ($resp) {
            foreach ($request->detalle_enfunde as $key => $det_enf):
                $det_enf = (object)$det_enf;
                $detalle = null;
                if (!$edit && !$request->edicion) {
                    $detalle = new ENF_DET_ENFUNDE();
                } else {
                    $detalle = ENF_DET_ENFUNDE::select('id', 'idenfunde', 'idseccion')
                        ->where('idenfunde', $enfunde->id)
                        ->where('idseccion', $det_enf->seccion)->first();
                }

                $detalle->idenfunde = $enfunde->id;
                $detalle->idseccion = $det_enf->seccion;
                if ($request->presente) {
                    $totaliza_presente += +intval($det_enf->presente);
                    $detalle->cantidad = $det_enf->presente;
                    $detalle->presente = 1;
                    $detalle->futuro = 0;
                } else {
                    $totaliza_futuro += +intval($det_enf->futuro);
                    $detalle->cantidad = $det_enf->futuro;
                    $detalle->desbunchado = $det_enf->desbunche;
                    $detalle->presente = 0;
                    $detalle->futuro = 1;
                }

                $resp = $detalle->save();
            endforeach;

            if ($resp) {
                if ($request->presente && $request->edicion) {
                    $enfunde->total_pre = $totaliza_presente;
                    $enfunde->total_fut = $totaliza_futuro;
                } else {
                    if ($request->futuro) {
                        $enfunde->count = 2;
                        $enfunde->status = 0;
                        $enfunde->total_fut = $request->total_fut;
                        $enfunde->chapeo = $request->desbunchado;
                    }
                }
                $enfunde->save();
            }
        }


        if ($resp)
            return $this->respuesta('success', 'Enfunde reportado correctamente');

        return $this->respuesta('error', 'Error al intentar guardar los datos');
    }

    public function respuesta($status, $messagge)
    {
        $data = [
            'status' => $status,
            'message' => $messagge
        ];

        return response()->json($data, 200);
    }

}
