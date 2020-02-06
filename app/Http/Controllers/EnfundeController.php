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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Mockery\Exception;
use function MongoDB\BSON\toJSON;

class EnfundeController extends Controller
{
    protected $perfil;
    protected $utilidades;
    protected $recursos;

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('AccesoURL',
            ['only' => ['index', 'form']]);
        date_default_timezone_set('America/Guayaquil');
        $this->perfil = new PerfilController();
        $this->utilidades = new UtilidadesController();
    }

    public function scopeSearch($q)
    {
        return empty(request()->search) ? $q : $q->where('name', 'like', '%' . request()->search . '%');
    }

    public function index($objeto, $modulo)
    {
        $hacienda_auth = Auth::user()->idHacienda;
        $hacienda = $hacienda_auth == 0 || $hacienda_auth == 1 ? 1 : 2;
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);

        $enfunde_pendiente = ENF_ENFUNDE::select('idhacienda', 'fecha', 'semana', 'periodo', 'cinta_pre', 'cinta_fut', 'idlotero', 'total_pre', 'total_fut', 'chapeo', 'status')
            ->with(['lotero' => function ($query) {
                $query->select('id', 'idhacienda', 'idempleado', 'nombre_1', 'nombre_2', 'apellido_1', 'apellido_2', 'nombres', 'labor');
            }])
            ->whereHas('lotero')
            ->where([
                'idhacienda' => $hacienda,
                'status' => 1
            ])
            ->search()
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        if (view()->exists('enfunde' . '.' . $objeto)) {
            return view('enfunde' . '.' . $objeto, [
                'objeto' => $objeto, 'modulo' => $modulo,
                'recursos' => $recursos,
                'semana' => $this->utilidades->getSemana(),
                'enfundes_pendientes' => $enfunde_pendiente,
                'loteros_pendientes' => $this->getLoteroPendientes($hacienda, $this->utilidades->getSemana()[0]->semana)
            ])->withInput(Input::all());
        } else {
            return redirect('/');
        }
    }

    public function form($objeto, $modulo)
    {
        $current_params = Route::current()->parameters();
        $hacienda = Auth::user()->idHacienda == 0 ? 1 : Auth::user()->idHacienda;
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);

        $data = [
            'objeto' => $objeto, 'modulo' => $modulo,
            'recursos' => $recursos,
            'semana' => $this->utilidades->getSemana(),
            'loteros' => $this->Loteros_nw($hacienda, $this->utilidades->getSemana()[0]->semana),
        ];


        return view('enfunde.enf_enfunde_registro', $data);
    }

    public function getLotero($idlotero, $semana)
    {
        $lotero = ENF_LOTERO::select('id', 'idempleado', 'nombres')
            ->where([
                'id' => $idlotero,
                'status' => 1
            ])
            ->with(['seccion' => function ($query) {
                $query->whereHas('lote', function ($query2) {
                    $query2->where('estado', 1);
                });
                $query->where('status', 1);
            }, 'seccion.lote'])
            ->with(['fundas' => function ($query3) use ($semana) {
                $query3->select('id', 'semana', 'idempleado', 'total', 'status');
                $query3->where('semana', $semana);
                $query3->with(['egresos' => function ($query7) {
                    $query7->select('id', 'id_egreso', 'fecha', 'idmaterial', 'cantidad', 'presente', 'futuro', 'status', 'reemplazo', 'idempleado');
                    $query7->with(['nom_reemplazo' => function ($query10) {
                        $query10->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
                        $query10->orderBy('NOMBRE_CORTO', 'asc');
                    }]);
                    $query7->with(['get_material' => function ($query) {
                        $query->select('id_fila', 'nombre');
                    }]);
                }]);
            }])
            ->with(['enfunde' => function ($query9) use ($semana) {
                $query9->select('id', 'idlotero', 'semana', 'total_pre', 'total_fut', 'count', 'status');
                $query9->where('semana', $semana);
                $query9->with(['detalle' => function ($query10) {
                    $query10->select('id', 'idenfunde', 'idseccion', 'id_material', 'cant_presente', 'cant_futuro', 'desbunchado', 'presente', 'futuro');
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

        /*if ($despacho) {

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
                $inventario = INV_LOT_FUND::select('id', 'idlotero', 'idmaterial', 'saldo', 'semana')->where([
                    'semana' => $semana,
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
                    $inventario_pre = INV_LOT_FUND::select('id', 'idlotero', 'idmaterial', 'saldo', 'semana')->where([
                        'idmaterial' => $material_presente,
                        'idlotero' => $idlotero,
                        'semana' => $semana
                    ])->first();

                    $inventario_fut = INV_LOT_FUND::select('id', 'idlotero', 'idmaterial', 'saldo', 'semana')->where([
                        'idmaterial' => $material_futuro,
                        'idlotero' => $idlotero,
                        'semana' => $semana
                    ])->first();

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
                    $inventario = INV_LOT_FUND::select('id', 'idlotero', 'idmaterial', 'saldo', 'semana')->where([
                        'semana' => $semana,
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

        }*/

        if ($despacho) {
            $materiales = INV_LOT_FUND::selectRaw('id, idlotero, semana, idmaterial, saldo, status, 0 as cantidad, 0 as cant_ocupada, saldo as saldo_backup')
                ->where([
                    'semana' => $semana,
                    'idlotero' => $idlotero,
                    'status' => true
                ])
                ->with(['material' => function ($query) {
                    $query->select('id_fila', 'nombre');
                }])
                ->get();
        }

        $lotero->push(['materiales' => $materiales]);

        return $lotero;
    }

    public function getLoteroPendientes($hacienda, $semana)
    {
        $loteros = array();
        $enf_loteros = ENF_ENFUNDE::select('id', 'idlotero')
            ->where([
                'idhacienda' => $hacienda,
                'semana' => $semana
            ])->get();

        foreach ($enf_loteros as $lot) {
            array_push($loteros, $lot->idlotero);
        }

        $loteros_pendiente = ENF_LOTERO::select('id', 'idhacienda', 'idempleado', 'status', 'nombres', 'labor')
            ->where([
                'idhacienda' => $hacienda,
                'status' => 1
            ])
            ->whereNotIn('id', $loteros)
            ->orderBy('nombres', 'asc')
            ->get();

        return $loteros_pendiente;
    }

    public function Loteros($hacienda, $semana)
    {
        $empleado = ENF_LOTERO::selectRaw('id, idempleado')
            ->with(['empleado' => function ($query) {
                $query->selectRaw('trim(NOMBRE_CORTO) as nombre, COD_TRABAJ');
                $query->where('ESTADO', 'A');
                $query->orderBy('nombre', 'asc');
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
            ->where([
                'idhacienda' => $hacienda,
                'status' => 1
            ])
            ->get();

        return $empleado;
    }

    public function Loteros_nw($hacienda, $semana)
    {
        $empleado = ENF_LOTERO::selectRaw('id, idempleado, nombres')
            ->with(['fundas' => function ($query3) use ($semana) {
                $query3->select('id', 'semana', 'idempleado', 'total', 'status');
                $query3->where('semana', $semana);
                $query3->with(['egresos' => function ($query7) {
                    $query7->select('id', 'id_egreso', 'fecha', 'cantidad', 'reemplazo', 'idempleado');
                }]);
            }])
            ->orderBy('nombres')
            ->where([
                'idhacienda' => $hacienda,
                'status' => 1
            ])
            ->get();

        return $empleado;
    }

    public function save(Request $request)
    {
        try {
            DB::beginTransaction();
            $resp = false;
            $edit = false;
            $delete = false;

            $json = $request->input('json');
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            if (!empty($params) && !empty($params_array)) {
                $validacion = \Validator::make($params_array, [
                    'fecha' => 'required',
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
                        if ($enfunde->total_pre > 0) {
                            $enfunde->save();
                        }
                    } else {
                        $edit = true;
                        $enfunde->id = $existe->id;
                    }

                    $totaliza_presente = 0;
                    $totaliza_futuro = 0;
                    $totaliza_desbunche = 0;

                    foreach ($params->detalle_enfunde as $item) {
                        $desbunchado = $item->desbunche;
                        $materiales = array();

                        if ($params_array['presente']) {
                            $materiales = $item->presente->materiales;
                        } else {
                            $materiales = $item->futuro->materiales;
                        }

                        foreach ($materiales as $material) {
                            $delete = false;
                            $rep_presente_backup = 0;
                            $rep_fut_backup = 0;

                            $existe_detalle = ENF_DET_ENFUNDE::select('id', 'idenfunde', 'idseccion', 'id_material', 'cant_presente', 'cant_futuro', 'desbunchado')
                                ->where([
                                    'idenfunde' => $enfunde->id,
                                    'idseccion' => $item->seccion,
                                    'id_material' => $material->idmaterial
                                ])->first();

                            if ($existe_detalle) {
                                $detalle = $existe_detalle;
                                $edit = true;

                                //Respaldar cantidad para editar en inventario de lotero funda saldo
                                $rep_presente_backup += +$detalle->cant_presente;
                                $rep_fut_backup += +$detalle->cant_futuro;
                            } else {
                                //En caso de que no exista el registro, se agrega uno nuevo
                                $detalle = new ENF_DET_ENFUNDE();
                                $detalle->idenfunde = $enfunde->id;
                                $detalle->id_material = $material->idmaterial;
                                $detalle->idseccion = $item->seccion;
                                $detalle->presente = $params_array['presente'];
                                $detalle->futuro = $params_array['futuro'];
                                $edit = false;
                            }

                            if ($params_array['presente']) {
                                if ($material->cantidad == 0 && $material->presente) {
                                    if ($edit) {
                                        if ($detalle->cant_futuro == 0) {
                                            $detalle->delete();
                                            $delete = true;
                                        } else {
                                            $detalle->cant_presente = $material->cantidad;
                                        }
                                    } else {
                                        $delete = true;
                                    }
                                } else {
                                    $detalle->cant_presente = $material->cantidad;
                                }
                            } else {
                                if ($material->cantidad == 0 && $material->futuro) {
                                    if ($edit) {
                                        if ($detalle->cant_presente == 0) {
                                            $detalle->delete();
                                            $delete = true;
                                        } else {
                                            $detalle->cant_futuro = $material->cantidad;
                                        }
                                    } else {
                                        $delete = true;
                                    }
                                } else {
                                    $detalle->cant_futuro = $material->cantidad;
                                    $detalle->desbunchado = +$desbunchado;
                                    $desbunchado = 0;
                                }

                            }

                            if ($material->presente) {
                                $totaliza_presente += $material->cantidad;
                            } elseif ($material->futuro) {
                                $totaliza_futuro += $material->cantidad;
                                $totaliza_desbunche += $item->desbunche;
                            }


                            $inventario = INV_LOT_FUND::select('id', 'idmaterial', 'idlotero', 'semana', 'saldo_inicial', 'entrada', 'salida', 'saldo')
                                ->where([
                                    'idmaterial' => $material->idmaterial,
                                    'idlotero' => $params_array['lotero'],
                                    'semana' => $params_array['semana']
                                ])->first();

                            if (!$edit) {
                                $inventario->salida = +$inventario->salida + +$material->cantidad;
                                $inventario->saldo = (+$inventario->saldo_inicial + +$inventario->entrada) - +$inventario->salida;
                            } else {
                                if ($material->presente) {
                                    $inventario->salida = (+$inventario->salida - +$rep_presente_backup) + +$material->cantidad;
                                } else {
                                    $inventario->salida = (+$inventario->salida - +$rep_fut_backup) + +$material->cantidad;
                                }
                                $inventario->saldo = (+$inventario->saldo_inicial + +$inventario->entrada) - +$inventario->salida;
                            }

                            $inventario->save();

                            if (!$delete)
                                $detalle->save();
                        }
                    }


                    //Totalizar cabecera
                    $existe = ENF_ENFUNDE::select('id', 'total_pre', 'total_fut', 'chapeo')->where([
                        'semana' => $params_array['semana'],
                        'idlotero' => $params_array['lotero']
                    ])->first();

                    if ($existe) {
                        if ($params_array['presente']) {
                            $existe->total_pre = $totaliza_presente;
                        } else {
                            if ($params_array['futuro']) {
                                $existe->total_fut = $totaliza_futuro;
                                $existe->chapeo = $totaliza_desbunche;
                            }
                        }
                        $existe->save();
                    }

                }
            }

            DB::commit();
            return $this->respuesta('success', 'Enfunde reportado correctamente');
        } catch (\PDOException $ex) {
            DB::rollBack();
            return $this->respuesta('error', $ex->getMessage());
        }
    }

    public function getMaterialPresente($lotero, $semana)
    {
        $lotero = ENF_LOTERO::select('id', 'idempleado')->where('id', $lotero)->first();
        $despacho = ENF_EGRESO::select('id', 'idempleado', 'semana')
            ->where([
                'idempleado' => $lotero->idempleado,
                'semana' => $semana
            ])->first();

        if ($despacho) {
            $despacho_detalle_pre = ENF_DET_EGRESO::select('id', 'id_egreso', 'idmaterial', 'cantidad', 'presente', 'futuro')
                ->where('presente', true)
                ->where('id_egreso', $despacho->id)->get();

            //Enlista materiales sacados en la semana presente
            return $material_presente = $this->utilidades->unique_multidim_array($despacho_detalle_pre, 'idmaterial');
        }

        return false;
    }

    public function getMaterialFuturo($lotero, $semana)
    {
        $lotero = ENF_LOTERO::select('id', 'idempleado')->where('id', $lotero)->first();
        $despacho = ENF_EGRESO::select('id', 'idempleado', 'semana')
            ->where([
                'idempleado' => $lotero->idempleado,
                'semana' => $semana
            ])->first();

        if ($despacho) {
            $despacho_detalle_fut = ENF_DET_EGRESO::select('id', 'id_egreso', 'idmaterial', 'cantidad', 'presente', 'futuro')
                ->where('futuro', true)
                ->where('id_egreso', $despacho->id)->get();

            //Enlista materiales sacados en la semana futuro
            return $material_futuro = $this->utilidades->unique_multidim_array($despacho_detalle_fut, 'idmaterial');
        }
        return false;
    }

    public function updateInventarioEnfunde($idlotero, $semana, $cantidad, $cantidad_anterior, $params = ['save' => true, 'presente' => 0, 'presente_backup' => 0])
    {
        try {
            //Actualizar el inventario
            $material_presente = $this->getMaterialPresente($idlotero, $semana);
            $material_futuro = $this->getMaterialFuturo($idlotero, $semana);

            //return response()->json($material_presente, 200);
            if (($material_presente && count($material_presente) > 0) || ($material_futuro && count($material_futuro) > 0)):

                $options = [
                    'new' => false,
                    'delete' => false,
                    'update' => false,
                    'salida' => true];

                $inventario = new EgresoController();

                if ($params['save']):
                    if (count($material_presente) == 1) {
                        if (count($material_futuro) == 1) {
                            if ($material_presente[0]->idmaterial == $material_futuro[0]->idmaterial) {
                                return $inventario->Inv_lotero($semana, $idlotero, $material_presente[0]->idmaterial,
                                    intval($cantidad), intval($cantidad_anterior), $options);
                            } else {
                                return $inventario->Inv_lotero($semana, $idlotero, $material_futuro[0]->idmaterial,
                                    intval($cantidad), intval($cantidad_anterior), $options);
                            }
                        } else {
                            return $inventario->Inv_lotero($semana, $idlotero, $material_presente[0]->idmaterial,
                                intval($cantidad), intval($cantidad_anterior), $options);
                        }
                    } else {
                        throw new Exception('No existen materiales para la semana presente');
                    }
                else:
                    $inventario->Inv_lotero($semana, $idlotero, $material_presente[0]->idmaterial,
                        $params['presente'],
                        $params['presente_backup'] > 0 ? $params['presente_backup'] : 0, $options);
                endif;

            endif;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete_presente($idlotero, $semana)
    {
        try {
            //Eliminar registro de enfunde presente
            if (!empty($idlotero) && !is_null($idlotero) && !empty($semana) && !is_null($semana)):
                DB::beginTransaction();

                $enfunde = ENF_ENFUNDE::select('id', 'semana', 'total_pre', 'total_fut')->where([
                    'idlotero' => $idlotero,
                    'semana' => $semana
                ])->first();

                if ($enfunde) {
                    if ($enfunde->total_pre > 0) {

                        //Siempre y cuando no hayan despachos para la futuro
                        $reg_futuro = ENF_DET_ENFUNDE::where([
                            'idenfunde' => $enfunde->id,
                            ['cant_futuro', '>', 0],
                        ])->get();

                        $despachos_futuro = [];
                        //Que no tengan despachos futuro----------------------------------------------------------------
                        $lotero = ENF_LOTERO::select('id', 'idempleado')
                            ->where('id', $idlotero)
                            ->first();

                        $egresos = ENF_EGRESO::where([
                            'idempleado' => $lotero->idempleado,
                            'semana' => $semana
                        ])->first();

                        if ($egresos) :
                            $despachos_futuro = ENF_DET_EGRESO::where([
                                'id_egreso' => $egresos->id,
                                'futuro' => 1
                            ])->get();
                        endif;
                        //----------------------------------------------------------------------------------------------

                        if (count($reg_futuro) == 0):
                            if (count($despachos_futuro) == 0):
                                $reg_presente = ENF_DET_ENFUNDE::select('id', 'idenfunde', 'id_material', 'cant_presente')->where([
                                    'idenfunde' => $enfunde->id,
                                    ['cant_presente', '>', 0],
                                ])->get();


                                if (count($reg_presente) > 0) :
                                    foreach ($reg_presente as $presente):
                                        $update_inventario = INV_LOT_FUND::select('id', 'idlotero', 'semana', 'idmaterial', 'saldo_inicial', 'entrada', 'salida', 'saldo')
                                            ->where([
                                                ['idlotero', $idlotero],
                                                ['semana', $semana],
                                                ['idmaterial', $presente->id_material]
                                            ])
                                            ->first();

                                        $update_inventario->salida = $update_inventario->salida - $presente->cant_presente;
                                        $update_inventario->saldo = (+$update_inventario->saldo_inicial + +$update_inventario->entrada) - +$update_inventario->salida;
                                        $update_inventario->save();
                                        $presente->delete();
                                    endforeach;

                                    $enfunde->delete();
                                    DB::commit();
                                    return Redirect::back()
                                        ->with('msg', 'Registro eliminado de manera correcta')
                                        ->with('status', 'success');
                                endif;
                            else:
                                throw new \Exception("Ya tiene despachos para cinta futuro.");
                            endif;
                        else:
                            throw new \Exception("Ya tiene despachado enfunde futuro.");
                        endif;
                    }
                }
                throw new \Exception("No se puede eliminar este registro.");
            endif;
        } catch (\PDOException $ex) {
            DB::rollBack();
            return Redirect::back()
                ->with('msg', $ex->getMessage())
                ->with('status', 'danger');
        } catch (\Exception $ex) {
            DB::rollBack();
            return Redirect::back()
                ->with('msg', $ex->getMessage())
                ->with('status', 'warning');
        }
    }

    public function delete_futuro($idlotero, $semana)
    {
        //Eliminar registro de enfunde futuro
        try {
            //Eliminar registro de enfunde presente
            if (!empty($idlotero) && !is_null($idlotero) && !empty($semana) && !is_null($semana)):
                DB::beginTransaction();
                $enfunde = ENF_ENFUNDE::select('id', 'semana', 'total_pre', 'total_fut')->where([
                    'idlotero' => $idlotero,
                    'semana' => $semana
                ])->first();

                if ($enfunde) {
                    if ($enfunde->total_fut > 0) {
                        $reg_futuro = ENF_DET_ENFUNDE::select('id', 'idenfunde', 'id_material', 'cant_futuro')
                            ->where([
                                ['idenfunde', '=', $enfunde->id],
                                ['cant_futuro', '>', 0],
                            ])->get();

                        if (count($reg_futuro) > 0) :
                            foreach ($reg_futuro as $futuro):
                                $update_inventario = INV_LOT_FUND::select('id', 'idlotero', 'semana', 'idmaterial', 'saldo_inicial', 'entrada', 'salida', 'saldo')
                                    ->where([
                                        ['idlotero', $idlotero],
                                        ['semana', $semana],
                                        ['idmaterial', $futuro->id_material]
                                    ])->first();

                                $update_inventario->salida = $update_inventario->salida - $futuro->cant_futuro;
                                $update_inventario->saldo = (+$update_inventario->saldo_inicial + +$update_inventario->entrada) - +$update_inventario->salida;
                                $update_inventario->save();
                            endforeach;

                            $eliminar = ENF_DET_ENFUNDE::where([
                                ['idenfunde', '=', $enfunde->id],
                                ['cant_futuro', '>', 0],
                            ])->delete();

                            if ($eliminar) {
                                $enfunde->total_fut = 0;
                                $enfunde->save();
                                DB::commit();
                                return Redirect::back()
                                    ->with('msg', 'Registro eliminado de manera correcta')
                                    ->with('status', 'success');
                            } else {
                                throw new \Exception("No se puede eliminar el registro.", 404);
                            }

                        endif;
                    } else {
                        throw new \Exception("Alerta!, No existe registro futuro.", 404);
                    }
                } else {
                    throw new \Exception("Error!, No se encuentra reistro de enfunde.", 404);
                }
            endif;
        } catch (\PDOException $ex) {
            DB::rollBack();
            return Redirect::back()
                ->with('msg', $ex->getMessage())
                ->with('status', 'danger');
        } catch (\Exception $ex) {
            DB::rollBack();
            return Redirect::back()
                ->with('msg', $ex->getMessage())
                ->with('status', 'danger');
        }
    }

    public function cerrar_enfunde($lotero, $semana, $status = true, $cerrar_todo = false)
    {
        try {
            DB::beginTransaction();
            $total_enfunde = 0;
            //Pasar el status a 0 de enfunde
            if ((!empty($lotero) || !is_null($lotero)) && (!empty($semana) || !is_null($semana))) {
                $resp = [
                    'code' => 500,
                    'status' => 'danger',
                    'message' => 'No se puede cerrar enfunde'
                ];
                $enfunde = ENF_ENFUNDE::select('id', 'idhacienda', 'semana', 'idlotero', 'total_pre', 'total_fut', 'count', 'status')
                    ->where([
                        'idlotero' => $lotero,
                        'semana' => $semana
                    ])->first();

                if ($enfunde && $enfunde->status == 1) {
                    if (+$enfunde->total_pre > 0 && +$enfunde->total_fut > 0) {
                        $total_enfunde = intval($enfunde->total_pre) + intval($enfunde->total_fut);
                        $enfunde->count = 2;
                        $enfunde->status = 0;
                        $status = $enfunde->save();

                        $lotero_empleado = ENF_LOTERO::select('id', 'idempleado')->where('id', $lotero)->first();
                        //Cerramos el despacho de semana
                        $despachos = ENF_EGRESO::select('id', 'semana', 'idempleado', 'saldo', 'status', 'total')
                            ->where([
                                'idempleado' => $lotero_empleado->idempleado,
                                'semana' => $semana
                            ])->first();

                        if ($despachos) {
                            $despachos->status = 0;
                            //$despachos->saldo = intval($despachos->total) - +$total_enfunde;
                            $despachos->saldo = 0;
                            $status = $despachos->save();

                            $despacho_detalles = ENF_DET_EGRESO::select('id', 'id_egreso', 'status')
                                ->where('id_egreso', $despachos->id)->update(array("status" => 0));

                            //Cerramos inventario
                            $inventario_lotero = INV_LOT_FUND::select('id', 'idlotero', 'semana', 'idmaterial', 'saldo_inicial', 'entrada', 'salida', 'saldo', 'status')
                                ->where([
                                    'idlotero' => $lotero,
                                    'semana' => $semana,
                                    'status' => 1
                                ])->get();

                            if (count($inventario_lotero) > 0) {
                                $inventario_lotero->status = 0;
                                foreach ($inventario_lotero as $item) {
                                    if (intval($item->saldo) > 0):
                                        $inventario = new INV_LOT_FUND();
                                        $inventario->idlotero = $item->idlotero;
                                        $inventario->semana = ($item->semana + 1) > 52 ? 1 : $item->semana + 1;
                                        $inventario->idmaterial = $item->idmaterial;
                                        $inventario->saldo_inicial = $item->saldo;
                                        $inventario->entrada = 0;
                                        $inventario->salida = 0;
                                        $inventario->saldo = $item->saldo;
                                        $inventario->status = 1;
                                        $status = $inventario->save();
                                    endif;
                                }

                                $update_inventario = INV_LOT_FUND::where([
                                    'idlotero' => $lotero,
                                    'semana' => $semana
                                ])->update(array("status" => 0));

                                if ($update_inventario) {
                                    $status = true;
                                    $resp['code'] = 200;
                                    $resp['status'] = 'success';
                                    $resp['message'] = 'Enfunde cerrado correctamente';
                                }
                            }
                        }
                    } else {
                        if (!$cerrar_todo)
                            throw new Exception('Falta dato de enfunde Futuro', 404);
                    }
                } else {
                    if (!$cerrar_todo)
                        throw new Exception('Enfunde ya se encuentra cerrado', 404);
                }
            }


            DB::commit();
            if ($status && !$cerrar_todo) {
                return Redirect::back()
                    ->with('msg', $resp['message'])
                    ->with('status', $resp['status']);
            } else {
                return $resp;
            }

        } catch (\PDOException $ex) {
            DB::rollBack();
            return Redirect::back()
                ->with('msg', $ex->getMessage())
                ->with('status', 'danger');
        } catch (\Exception $ex) {
            DB::rollBack();
            return Redirect::back()
                ->with('msg', $ex->getMessage())
                ->with('status', 'warning');
        }
    }

    public function cerrar_enfundeAll($idhacienda)
    {
        $cerrados = array();
        $no_se_cierran = array();
        $resp = array();


        $idhacienda = $idhacienda == 0 || $idhacienda == 1 ? 1 : 2;

        $enfunde_open = ENF_ENFUNDE::select('id', 'semana', 'idlotero')
            ->where([
                'idhacienda' => $idhacienda,
                'status' => 1
            ])->get();

        foreach ($enfunde_open as $enfunde) {
            $resp = $this->cerrar_enfunde($enfunde->idlotero, $enfunde->semana, true, true);
            if ($resp['code'] == 200 || $resp['code'] == 404) {
                array_push($cerrados, true);
            } else {
                array_push($no_se_cierran, true);
            }
        }

        $message = "Se pudo cerrar " . count($cerrados) . " enfundes, y quedan abiertos " . count($no_se_cierran);

        return Redirect::back()
            ->with('msg', $message)
            ->with('status', 'warning');
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
