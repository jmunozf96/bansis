<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\Sisban\Enfunde\ENF_ENFUNDE;
use App\Sisban\Enfunde\ENF_LOTERO;
use App\Sisban\Enfunde\INV_LOT_FUND;
use App\Sisban\Enfunde\ENF_DET_EGRESO;
use App\Sisban\Enfunde\ENF_EGRESO;
use Hashids\Hashids;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EgresoController extends Controller
{
    protected $perfil;
    protected $utilidades;
    protected $recursos;
    protected $enfunde;

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('AccesoURL',
            ['only' => ['index', 'form', 'index2']]);
        date_default_timezone_set('America/Guayaquil');
        $this->perfil = new PerfilController();
        $this->utilidades = new UtilidadesController();
        $this->enfunde = new EnfundeController();
    }

    public function index2($objeto, $modulo)
    {
        $hacienda_auth = Auth::user()->idHacienda;
        $hacienda = $hacienda_auth == 0 || $hacienda_auth == 1 ? 1 : 2;
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);

        $egresos = ENF_EGRESO::select('id', 'idempleado', 'total', 'status')
            ->where('semana', $this->utilidades->getSemana()[0]->semana)
            ->where('idhacienda', $hacienda)
            ->with(['lotero' => function ($query) {
                $query->select('id', 'idhacienda', 'idempleado', 'nombre_1', 'nombre_2', 'apellido_1', 'apellido_2', 'nombres');
            }])
            ->with(['egresos' => function ($query) {
                $query->select('id', 'id_egreso', 'fecha', 'idmaterial', 'cantidad', 'presente', 'futuro', 'status');
                $query->with(['get_material' => function ($query1) {
                    $query1->selectRaw('id_fila,rtrim(codigo) codigo, nombre, bodegacompra');
                }]);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(5);


        if (view()->exists('enfunde' . '.' . $objeto)) {
            return view('enfunde' . '.' . $objeto, [
                'objeto' => $objeto, 'modulo' => $modulo,
                'recursos' => $recursos,
                'semana' => $this->utilidades->getSemana(),
                'egresos' => $egresos
            ]);
        } else {
            return redirect('/');
        }

    }

    public function index($objeto, $recursos)
    {
        $this->recursos = $recursos;

        //Caso de ser 0 la hacienda

        $egresos = ENF_EGRESO::select('id', 'idempleado', 'total', 'status')
            ->where('semana', $this->utilidades->getSemana()[0]->semana)
            ->where('idhacienda', Auth::user()->idHacienda == 0 ? 1 : Auth::user()->idHacienda)
            ->with(['lotero'])
            ->with(['egresos' => function ($query) {
                $query->select('id', 'id_egreso', 'fecha', 'idmaterial', 'cantidad', 'presente', 'futuro', 'status');
                $query->with(['get_material' => function ($query1) {
                    $query1->selectRaw('id_fila,rtrim(codigo) codigo,nombre,bodegacompra');
                }]);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        if (view()->exists('enfunde' . '.' . $objeto)) {
            return view('enfunde' . '.' . $objeto, [
                'recursos' => $this->recursos,
                'semana' => $this->utilidades->getSemana(),
                'egresos' => $egresos
            ]);
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
            'bodegas' => $this->utilidades->Bodegas(),
            'materiales' => $this->utilidades->getcomboProductos(13),
            'loteros' => $this->enfunde->Loteros_nw($hacienda, $this->utilidades->getSemana()[0]->semana)
        ];
        return view('enfunde.enf_egreso_material', $data);
    }

    public function save(Request $request)
    {
        $resp = false;

        //En caso de que se vaya añadir un nuevo item, solo se añadera el detalle
        $totalizar = 0;
        $edit = false;

        $json = $request->input('json');
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $resp = [
            'code' => 500,
            'status' => 'error',
            'message' => $params_array
        ];

        if (!empty($params_array) && !empty($params)) {
            $validacion = \Illuminate\Support\Facades\Validator::make($params_array, [
                'fecha' => 'required',
                'semana' => 'required',
                'hacienda' => 'required',
                'idempleado' => 'required',
                'despachos' => 'required|array',
                'despachos.*' => 'required'
            ]);

            if (!$validacion->fails()) {

                $lotero = ENF_LOTERO::where([
                    'idempleado' => $params_array['idempleado']
                ])->first();

                $despacho = new ENF_EGRESO();
                $despacho->fecha = $params_array['fecha'];
                $despacho->semana = $params_array['semana'];
                $despacho->idhacienda = $params_array['hacienda'] == '343' ? 1 : 2;
                $despacho->idempleado = $params_array['idempleado'];
                $despacho->total = $params_array['total'];
                $despacho->saldo = $params_array['saldo'];
                $despacho->status = 1;

                $existe = ENF_EGRESO::where([
                    'semana' => $despacho->semana,
                    'idempleado' => $despacho->idempleado,
                    'idhacienda' => $despacho->idhacienda
                ])->first();

                if (!$existe) {
                    $despacho->save();
                    $resp['code'] = 202;
                    $resp['status'] = 'success';
                    $resp['message'] = 'Registro guardado correctamente';
                } else {
                    $despacho->id = $existe->id;
                    $edit = true;
                }

                foreach ($params->despachos as $key => $item):
                    $detalle = null;
                    $nuevo = true;

                    if ($edit) {
                        $detalle = ENF_DET_EGRESO::select('id', 'id_egreso', 'fecha', 'idmaterial', 'cantidad', 'presente', 'futuro', 'reemplazo', 'idempleado')->where([
                            'id_egreso' => $despacho->id,
                            'fecha' => $item->fecha,
                            'idmaterial' => $item->idmaterial,
                            'presente' => $item->presente,
                            'futuro' => $item->futuro,
                            'reemplazo' => $item->reemplazo,
                            'idempleado' => $item->idempleado
                        ])->first();

                        if ($detalle) {
                            $nuevo = false;
                        } else {
                            $nuevo = true;
                        }
                    } else {
                        $nuevo = true;
                    }

                    if ($nuevo) {
                        $detalle = new ENF_DET_EGRESO();
                        $detalle->id_egreso = $despacho->id;
                        $detalle->fecha = $item->fecha;
                        $detalle->idbodega = 0;
                        $detalle->idmaterial = $item->idmaterial;
                        $detalle->reemplazo = $item->reemplazo;
                        $detalle->idempleado = $item->idempleado;
                        $detalle->cantidad = $item->cantidad;
                        $detalle->presente = $item->presente;
                        $detalle->futuro = $item->futuro;
                        $detalle->status = 1;

                        $options = [
                            'new' => true,
                            'delete' => false,
                            'update' => false,
                            'salida' => false];

                        //Registramos inventario
                        $this->Inv_lotero($params_array['semana'], $lotero->id, $item->idmaterial, $item->cantidad, 0, $options);
                    }


                    if ($detalle) {
                        $enfunde_reportado = ENF_ENFUNDE::where([
                            'idlotero' => $lotero->id,
                            'semana' => $params_array['semana']
                        ])->first();

                        $bool = false;

                        if (!$nuevo && !is_null($enfunde_reportado)) {
                            if ($detalle->presente && +$enfunde_reportado->total_pre > 0) {
                                $bool = true;
                            } else {
                                if ($detalle->futuro && +$enfunde_reportado->total_fut > 0) {
                                    $bool = true;
                                }
                            }
                        }

                        if (!$bool) {
                            $options = [
                                'new' => false,
                                'delete' => false,
                                'update' => true,
                                'salida' => false];

                            //Registramos inventario
                            $this->Inv_lotero($params_array['semana'], $lotero->id, $item->idmaterial, $item->cantidad, $detalle->cantidad, $options);
                            $detalle->cantidad = $item->cantidad;
                            $detalle->save();
                        }

                        $resp['code'] = 202;
                        $resp['status'] = 'success';
                        $resp['message'] = 'Registro guardado correctamente';
                    }

                    $totalizar += $edit ? +$item->cantidad : 0;
                endforeach;

                if ($edit) {
                    $despacho_cabecera = ENF_EGRESO::select('id', 'idempleado', 'total')->where([
                        'id' => $despacho->id,
                        'idempleado' => $despacho->idempleado
                    ])->first();

                    $despacho_cabecera->total = $totalizar;
                    $despacho_cabecera->save();
                }

                $resp['render'] = $this->renderSelectLotero();
            }
        }

        return response()->json($resp, $resp['code']);
    }

    public function deleteDetalle($codigo)
    {
        $hashid = new Hashids('', 50, '0123456789abcdefghijklmnopqrstuvwxyz');
        $id = $hashid->decode($codigo)[0];

        //PRESENTE O FUTURO
        $detalle = ENF_DET_EGRESO::select('id', 'id_egreso', 'idmaterial', 'cantidad', 'presente', 'futuro')->where('id', $id)->first();
        $egreso = ENF_EGRESO::select('id', 'total', 'idempleado', 'semana')->where('id', $detalle->id_egreso)->first();

        $lotero = ENF_LOTERO::where('idempleado', $egreso->idempleado)->first();

        $existe_enfunde = ENF_ENFUNDE::where([
            'semana' => $egreso->semana,
            'idlotero' => $lotero->id
        ])->first();


        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'Registro eliminado con éxito!',
            'render' => $this->renderSelectLotero()
        ];

        if ($existe_enfunde) {
            if ($detalle->presente) {
                if (+$existe_enfunde->total_pre > 0) {
                    $data['code'] = 405;
                    $data['status'] = 'error';
                    $data['message'] = 'No se puede eliminar, ya se reporto enfunde presente';
                    return \response()->json($data, 200);
                }
            } else {
                if (+$existe_enfunde->total_fut > 0) {
                    $data['code'] = 405;
                    $data['status'] = 'error';
                    $data['message'] = 'No se puede eliminar, ya se reporto enfunde futuro';
                    return \response()->json($data, 200);
                }
            }
        }

        $egreso->total = $egreso->total - $detalle->cantidad;
        $options = [
            'new' => false,
            'delete' => true,
            'update' => false,
            'salida' => false];

        //Registramos inventario
        $this->Inv_lotero($egreso->semana, $lotero->id, $detalle->idmaterial, $detalle->cantidad, 0, $options);

        $detalle->delete();
        if ($egreso->total == 0) {
            $egreso->delete();
        } else {
            $egreso->save();
        }

        return response()->json($data, 200);
    }

    public function renderSelectLotero()
    {
        $hacienda = Auth::user()->idHacienda == 0 ? 1 : Auth::user()->idHacienda;
        $html = view('enfunde.select_lotero', compact('view'))->with([
            'loteros' => $this->enfunde->Loteros_nw($hacienda, $this->utilidades->getSemana()[0]->semana)
        ])->render();

        return compact('html');
    }

    public function getdespacho($empleado, $semana, $hacienda, $axios = 0)
    {
        if (!empty($semana) && !empty($hacienda)):
            $despacho = ENF_EGRESO::select('id', 'fecha', 'idempleado', 'semana', 'idhacienda')
                ->where('idempleado', $empleado)
                ->where('semana', $semana)
                ->where('idhacienda', $hacienda == '343' ? 1 : 2)
                ->with(['lotero' => function ($query3) use ($semana) {
                    $query3->with(['enfunde' => function ($query) use ($semana) {
                        $query->select('id', 'total_pre', 'total_fut', 'idlotero', 'status', 'count')
                            ->where('semana', $semana);
                    }]);
                }])
                ->with(['egresos' => function ($query) {
                    $query->select('id', 'id_egreso', 'fecha', 'idmaterial', 'reemplazo', 'idempleado', 'cantidad', 'presente', 'futuro', 'status');
                    $query->with(['get_material' => function ($query1) {
                        $query1->selectRaw('id_fila,rtrim(codigo) codigo,nombre,bodegacompra');
                    }]);
                    $query->with(['nom_reemplazo' => function ($query2) {
                        $query2->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
                    }]);
                    $query->orderBy('fecha');
                }])
                ->first();

            if ($despacho) {
                $hashid = new Hashids('', 50, '0123456789abcdefghijklmnopqrstuvwxyz');
                $id = $hashid->encode($despacho->id);
                unset($despacho->id);
                $despacho->idhash = $id;
                $despacho->decode = $hashid->decode($id)[0];
            }

            //Si es una peticion desde el lado del cliente
            if ($axios && $despacho) {
                $despacho->toArray();
                $despacho->fecha = date("d/m/Y", strtotime($despacho->fecha));
                foreach ($despacho->egresos as $egreso):
                    $egreso->fecha = date("d/m/Y", strtotime($egreso->fecha));
                endforeach;
            }

            return $despacho;
        else:
            return null;
        endif;
    }

    public function respuesta($status, $messagge)
    {
        $data = [
            'status' => $status,
            'message' => $messagge
        ];

        return response()->json($data, 200);
    }

    public function saldopendiente($idempleado, $idmaterial, $semana)
    {
        $idlotero = ENF_LOTERO::select('id', 'idempleado')->where('idempleado', $idempleado)->first();
        $inventario = INV_LOT_FUND::select('idlotero', 'semana', 'saldo_inicial', 'salida', 'saldo', 'idmaterial', 'status')
            ->where([
                'idlotero' => $idlotero->id,
                'idmaterial' => $idmaterial,
                'semana' => $semana,
                'status' => 1
            ])->first();

        return $inventario;
    }

    public function Inv_lotero($semana, $lotero, $material, $cantidad, $cant_anterior = 0, $options = [
        'new' => true,
        'delete' => false,
        'update' => false,
        'salida' => false])
    {
        try {
            if (!is_null($lotero) && !is_null($material) && !is_null($cantidad)) {
                DB::beginTransaction();
                $inventario = INV_LOT_FUND::select('id', 'idlotero', 'semana', 'idmaterial', 'saldo_inicial', 'entrada', 'salida', 'saldo', 'status')
                    ->where([
                        'idlotero' => $lotero,
                        'idmaterial' => $material,
                        'semana' => $semana
                    ])->first();

                if (!$inventario) {
                    $inventario = new INV_LOT_FUND();
                    $inventario->semana = $semana;
                    $inventario->idlotero = $lotero;
                    $inventario->idmaterial = $material;
                    $inventario->saldo_inicial = 0;
                    $inventario->status = 1;
                }

                switch ($options) {
                    case $options['new']:
                        $inventario->entrada += $cantidad;
                        $inventario->saldo = ($inventario->saldo_inicial + $inventario->entrada) - $inventario->salida;
                        $inventario->save();
                        DB::commit();
                        return true;
                    case $options['update']:
                        $inventario->entrada = $inventario->entrada - $cant_anterior;
                        $inventario->entrada += $cantidad;
                        $inventario->saldo = ($inventario->saldo_inicial + $inventario->entrada) - $inventario->salida;
                        $inventario->save();
                        DB::commit();
                        return true;
                    case $options['delete']:
                        $inventario->entrada = $inventario->entrada - $cantidad;
                        $inventario->saldo = ($inventario->saldo_inicial + $inventario->entrada) - $inventario->salida;
                        $inventario->save();
                        if ($inventario->saldo == 0) {
                            $inventario->delete();
                        }
                        DB::commit();
                        return true;
                    case $options['salida']:
                        if ($cant_anterior > 0) {
                            $inventario->salida = $inventario->salida - $cant_anterior;
                        }
                        $inventario->salida += $cantidad;
                        $inventario->saldo = ($inventario->saldo_inicial + $inventario->entrada) - $inventario->salida;
                        $inventario->save();
                        DB::commit();
                        return true;
                }
            }
        } catch (\PDOException $ex) {
            DB::rollBack();
            return false;
        }
    }
}
