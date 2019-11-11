<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\Sisban\Enfunde\ENF_DET_EGRESO;
use App\Sisban\Enfunde\ENF_DET_ENFUNDE;
use App\Sisban\Enfunde\ENF_EGRESO;
use App\Sisban\Enfunde\ENF_ENFUNDE;
use App\Sisban\Enfunde\ENF_LOTERO;
use App\Sisban\Enfunde\INV_LOT_FUND;
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
        /*$this->middleware('auth');
        $this->middleware('AccesoURL',
            ['except' => ['getLotero', 'Loteros', 'save']]);*/
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
            'loteros' => $this->Loteros($hacienda, $this->utilidades->getSemana()[0]->semana),
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
                    $query7->select('id', 'id_egreso', 'fecha', 'idmaterial', 'cantidad', 'presente', 'futuro', 'status', 'reemplazo', 'idempleado');
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


        $materiales = array();
        $empleado = ENF_LOTERO::select('id', 'idempleado')->where('id', $idlotero)->first();
        $despacho = ENF_EGRESO::select('id', 'idempleado', 'semana')
            ->where([
                'idempleado' => $empleado->idempleado,
                'semana' => $semana
            ])->first();

        if ($despacho) {

            $despacho_detalle_pre = ENF_DET_EGRESO::select('id', 'id_egreso', 'idmaterial', 'cantidad', 'presente', 'futuro')
                ->where('presente', true)
                ->where('id_egreso', $despacho->id)->get();

            $despacho_detalle_fut = ENF_DET_EGRESO::select('id', 'id_egreso', 'idmaterial', 'cantidad', 'presente', 'futuro')
                ->where('futuro', true)
                ->where('id_egreso', $despacho->id)->get();

            $material_presente = '';
            $material_futuro = '';
            if ($despacho_detalle_pre) {
                $material_presente = $this->utilidades->unique_multidim_array($despacho_detalle_pre, 'idmaterial');
                if (count($material_presente) > 0) {
                    $material_presente = $material_presente[0]->idmaterial;
                }
            }

            if ($despacho_detalle_fut) {
                $material_futuro = $this->utilidades->unique_multidim_array($despacho_detalle_fut, 'idmaterial');
                if (count($material_futuro) > 0)
                    $material_futuro = $material_futuro[0]->idmaterial;
            }

            if ($material_presente == $material_futuro) {
                $inventario = INV_LOT_FUND::select('id', 'idlotero', 'idmaterial', 'saldo')->where([
                    'idmaterial' => $material_presente,
                    'idlotero' => $idlotero])->first();

                if ($inventario)
                    $materiales = [
                        'semana' => [
                            'idmaterial' => $material_presente,
                            'saldo' => $inventario->saldo
                        ]
                    ];

            } else {
                if (!empty($material_futuro)) {
                    $inventario_pre = INV_LOT_FUND::select('id', 'idlotero', 'idmaterial', 'saldo')->where([
                        'idmaterial' => $material_presente,
                        'idlotero' => $idlotero])->first();

                    $inventario_fut = INV_LOT_FUND::select('id', 'idlotero', 'idmaterial', 'saldo')->where([
                        'idmaterial' => $material_futuro,
                        'idlotero' => $idlotero])->first();

                    $materiales = [
                        'presente' => [
                            'idmaterial' => $material_presente,
                            'saldo' => $inventario_pre->saldo,
                        ],
                        'futuro' => [
                            'idmaterial' => $material_futuro,
                            'saldo' => $inventario_fut->saldo,
                        ]
                    ];
                } else {
                    $inventario = INV_LOT_FUND::select('id', 'idlotero', 'idmaterial', 'saldo')->where([
                        'idmaterial' => $material_presente,
                        'idlotero' => $idlotero])->first();

                    if ($inventario)
                        $materiales = [
                            'semana' => [
                                'idmaterial' => $material_presente,
                                'saldo' => $inventario->saldo
                            ]
                        ];
                }
            };

        }

        $lotero->push(['materiales' => $materiales]);
        return $lotero;
    }

    public function Loteros($hacienda, $semana)
    {
        $empleado = ENF_LOTERO::selectRaw('id, idempleado')
            ->with(['empleado' => function ($query) {
                $query->selectRaw('trim(NOMBRE_CORTO) as nombre, COD_TRABAJ');
                $query->where('ESTADO', 'A');
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
            ->where('idhacienda', $hacienda)
            ->get();

        return $empleado;
    }

    public function save(Request $request)
    {
        $resp = false;
        $edit = false;

        $json = $request->input('json');
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            $validacion = \Validator::make($params_array, [
                'fecha' => 'required|date',
                'semana' => 'required',
                'lotero' => 'required',
                'detalle_enfunde' => 'required|array',
                'detalle_enfunde.*' => 'required'
            ]);

            if (!$validacion->fails()) {
                $lotero = ENF_LOTERO::select('id', 'idempleado')->where('id', $params_array['lotero'])->first();

                $enfunde = new ENF_ENFUNDE();
                $enfunde->idhacienda = $params_array['idhacienda'] == '343' ? 1 : 2;
                $enfunde->fecha = $params_array['fecha'];
                $enfunde->semana = $params_array['semana'];
                $enfunde->periodo = $params_array['periodo'];
                $enfunde->cinta_pre = $this->utilidades->getSemana()[0]->cod_color;
                $enfunde->cinta_fut = $this->utilidades->getSemana()[1]->cod_color;
                $enfunde->idlotero = $params_array['lotero'];
                $enfunde->total_pre = $params_array['total_pre'];
                $enfunde->total_fut = $params_array['total_fut'];
                $enfunde->chapeo = $params_array['desbunchado'];
                $enfunde->count = $params_array['presente'] ? 1 : 2;
                $enfunde->status = 1;

                $existe = ENF_ENFUNDE::select('id', 'total_pre', 'total_fut', 'chapeo')->where([
                    'semana' => $params_array['semana'],
                    'idlotero' => $params_array['lotero']
                ])->first();

                if (!$existe) {
                    $enfunde->save();
                } else {
                    $edit = true;
                    $enfunde->id = $existe->id;
                }

                $totaliza_presente = 0;
                $totaliza_futuro = 0;
                $totaliza_desbunche = 0;

                $rep_presente_backup = 0;
                $rep_fut_backup = 0;

                foreach ($params->detalle_enfunde as $item) {
                    $existe_detalle = ENF_DET_ENFUNDE::select('id', 'idenfunde', 'idseccion', 'cantidad', 'desbunchado', 'presente', 'futuro')
                        ->where([
                            'idenfunde' => $enfunde->id,
                            'idseccion' => $item->seccion,
                            'presente' => $params_array['presente'],
                            'futuro' => $params_array['futuro']
                        ])->first();

                    if ($existe_detalle) {
                        $detalle = $existe_detalle;
                        $edit = true;

                        if ($detalle->presente) {
                            $rep_presente_backup += $detalle->cantidad;
                        } else {
                            $rep_fut_backup += $detalle->cantidad;
                        }

                    } else {
                        $detalle = new ENF_DET_ENFUNDE();
                        $detalle->idenfunde = $enfunde->id;
                        $detalle->idseccion = $item->seccion;
                        $detalle->presente = $params_array['presente'];
                        $detalle->futuro = $params_array['futuro'];
                        $edit = false;
                    }

                    if ($params_array['presente']) {
                        $detalle->cantidad = $item->presente;
                    } else {
                        $detalle->cantidad = $item->futuro;
                        $detalle->desbunchado = $item->desbunche;
                    }

                    $totaliza_presente += $item->presente;
                    $totaliza_futuro += $item->futuro;
                    $totaliza_desbunche += $item->desbunche;
                    $detalle->save();
                }

                $despacho = ENF_EGRESO::select('id', 'idempleado', 'semana')
                    ->where([
                        'idempleado' => $lotero->idempleado,
                        'semana' => $params_array['semana']
                    ])->first();

                if ($despacho) {
                    $despacho_detalle_pre = ENF_DET_EGRESO::select('id', 'id_egreso', 'idmaterial', 'cantidad', 'presente', 'futuro')
                        ->where('presente', true)
                        ->where('id_egreso', $despacho->id)->get();

                    $despacho_detalle_fut = ENF_DET_EGRESO::select('id', 'id_egreso', 'idmaterial', 'cantidad', 'presente', 'futuro')
                        ->where('futuro', true)
                        ->where('id_egreso', $despacho->id)->get();

                    //Enlista materiales sacados en la semana presente y futuro
                    $material_presente = $this->utilidades->unique_multidim_array($despacho_detalle_pre, 'idmaterial');
                    $material_futuro = $this->utilidades->unique_multidim_array($despacho_detalle_fut, 'idmaterial');

                    $options = [
                        'new' => false,
                        'delete' => false,
                        'update' => false,
                        'salida' => true];

                    $inventario = new EgresoController();

                    if ($totaliza_presente > 0) {
                        if ($totaliza_futuro > 0) {
                            if (count($material_presente) == 1) {
                                if (count($material_futuro) == 1) {
                                    if ($material_presente[0]->idmaterial == $material_futuro[0]->idmaterial) {
                                        $inventario->Inv_lotero($lotero->id, $material_presente[0]->idmaterial,
                                            $totaliza_futuro,
                                            $rep_fut_backup > 0 ? $rep_fut_backup : 0,
                                            $options);
                                    } else {
                                        $inventario->Inv_lotero($lotero->id, $material_futuro[0]->idmaterial,
                                            $totaliza_futuro,
                                            $rep_fut_backup > 0 ? $rep_fut_backup : 0, $options);
                                    }
                                }
                            }
                        } else {
                            $inventario->Inv_lotero($lotero->id, $material_presente[0]->idmaterial,
                                $totaliza_presente,
                                $rep_presente_backup > 0 ? $rep_presente_backup : 0, $options);
                        }

                    }
                }

                //Totalizar cabecera
                $existe = ENF_ENFUNDE::select('id', 'total_pre', 'total_fut', 'chapeo')->where([
                    'semana' => $params_array['semana'],
                    'idlotero' => $params_array['lotero']
                ])->first();

                if ($existe) {
                    $existe->total_pre = $totaliza_presente;
                    $existe->total_fut = $totaliza_futuro;
                    $existe->chapeo = $totaliza_desbunche;
                    $existe->save();
                }

            }
        }

        //Pasar status de egresos a 0

        return $this->respuesta('success', 'Enfunde reportado correctamente');
    }

    public
    function respuesta($status, $messagge)
    {
        $data = [
            'status' => $status,
            'message' => $messagge
        ];

        return response()->json($data, 200);
    }

}
