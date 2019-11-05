<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\Sisban\Enfunde\ENF_LOTERO;
use App\Sisban\Enfunde\INV_LOT_FUND;
use App\Sisban\Enfunde\ENF_DET_EGRESO;
use App\Sisban\Enfunde\ENF_EGRESO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ['except' => ['save', 'getdespacho', 'deleteDetalle', 'editDetalle', 'respuesta', 'saldopendiente']]);
        date_default_timezone_set('America/Guayaquil');
        $this->perfil = new PerfilController();
        $this->utilidades = new UtilidadesController();
        $this->enfunde = new EnfundeController();
    }

    public function index($objeto, $recursos)
    {
        $this->recursos = $recursos;

        //Caso de ser 0 la hacienda

        $egresos = ENF_EGRESO::select('id', 'idempleado', 'total', 'status')
            ->where('semana', $this->utilidades->getSemana()[0]->semana)
            ->where('idhacienda', Auth::user()->idhacienda == 0 ? 1 : Auth::user()->idhacienda)
            ->with(['empleado' => function ($query) {
                $query->selectRaw('COD_TRABAJ, CONCAT(trim(APELLIDO_1)," ",trim(NOMBRE_1)) as nombre');
            }])
            ->with(['egresos' => function ($query) {
                $query->select('id', 'id_egreso', 'fecha', 'idmaterial', 'cantidad', 'presente', 'futuro', 'status');
                $query->with(['get_material' => function ($query1) {
                    $query1->selectRaw('id_fila,rtrim(codigo) codigo,nombre,bodegacompra');
                }]);
            }])
            ->paginate(6);

        if (view()->exists('enfunde' . '.' . $objeto)) {
            return view('enfunde' . '.' . $objeto, [
                'recursos' => $this->recursos,
                'semana' => $this->utilidades->getSemana(),
                'egresos' => $egresos
            ]);
        }
    }

    public function form(Request $request)
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
            'bodegas' => $this->utilidades->Bodegas(),
            'loteros' => $this->enfunde->Loteros($hacienda, $this->utilidades->getSemana()[0]->semana)
        ];
        return view('enfunde.enf_egreso_material', $data);
    }

    public function save(Request $request)
    {
        $resp = false;

        //En caso de que se vaya aññadir un nuevo item, solo se añadera el detalle
        $totalizar = 0;
        $edit = false;

        $despacho = new ENF_EGRESO();

        $despacho->fecha = $request->fecha;
        $despacho->semana = $request->semana;
        $despacho->idhacienda = $request->hacienda == '343' ? 1 : 2;
        $despacho->idempleado = $request->idempleado;
        $despacho->total = $request->total;
        $despacho->saldo = $request->saldo;
        $despacho->status = 1;

        $despacho_existe = $this->getdespacho($request->idempleado, $request->semana, $request->hacienda);

        //Pregunta si este empleado ya tiene un despacho abierto en esta semana
        $resp = $despacho_existe ? true : false;
        if ($resp) {
            $despacho->id = $despacho_existe->id;
            $edit = true;
        } else {
            $resp = $despacho->save();
        }

        if ($resp) {
            foreach ($request->despachos as $key => $item):
                $item = (object)$item;
                if (!isset($item->id)) {
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
                    $resp = $detalle->save();

                } else {
                    $detalle = ENF_DET_EGRESO::select('id', 'cantidad')->find($item->id);
                    $detalle->cantidad = $item->cantidad;
                    $detalle->save();
                }

                $totalizar += $edit ? +$item->cantidad : 0;
            endforeach;

            if ($edit) {
                $despacho_cabecera = ENF_EGRESO::select('id', 'total')->find($despacho->id);
                $despacho_cabecera->total = $totalizar;
                $despacho_cabecera->save();
            }

            //Guardar saldo en inventario
            foreach ($this->utilidades->unique_multidim_array($request->despachos, 'idmaterial') as $key2 => $item2):
                $item2 = (object)$item2;

                $saldo_pre = 0;
                $saldo_fut = 0;
                $presente = false;
                $futuro = false;

                foreach ($request->despachos as $key => $item):
                    $item = (object)$item;
                    if ($item->idmaterial == $item2->idmaterial) {
                        if ($item->presente) {
                            $presente = true;
                            $saldo_pre += $item->cantidad;
                        } else {
                            if ($item->futuro) {
                                $futuro = true;
                                $saldo_fut += $item->cantidad;
                            }
                        }
                    }
                endforeach;

                $lotero = ENF_LOTERO::select('id', 'idempleado')->where('idempleado', $request->idempleado)->first();
                $request->idempleado = $lotero->id;

                //Validar cuando sea presente o futuro
                if ($presente) {
                    $validator = \Validator::make($request->all(), [
                        'semana' => ['required',
                            Rule::unique('INV_LOT_FUND')->where(function ($query) use ($request, $item2) {
                                return $query->where('semana', $request->semana)
                                    ->where('idlotero', $request->idempleado)
                                    ->where('idmaterial', $item2->idmaterial)
                                    ->where('presente', true);
                            })]
                    ]);

                    if ($validator->fails()) {
                        $inventario = INV_LOT_FUND::select('id', 'semana', 'idlotero', 'idmaterial', 'saldo', 'presente', 'futuro')
                            ->where('semana', $request->semana)
                            ->where('idlotero', $request->idempleado)
                            ->where('idmaterial', $item2->idmaterial)
                            ->where('presente', true)->first();
                    } else {
                        $inventario = new INV_LOT_FUND();
                        $inventario->semana = $request->semana;
                        $inventario->idlotero = $request->idempleado;
                        $inventario->idmaterial = $item2->idmaterial;
                        $inventario->presente = true;
                        $inventario->futuro = false;
                    }

                    $inventario->saldo = $saldo_pre;

                    $inventario->status = true;
                    $inventario->save();
                }

                if ($futuro) {
                    $validator2 = \Validator::make($request->all(), [
                        'semana' => ['required',
                            Rule::unique('INV_LOT_FUND')->where(function ($query) use ($request, $item2) {
                                return $query->where('semana', $request->semana)
                                    ->where('idlotero', $request->idempleado)
                                    ->where('idmaterial', $item2->idmaterial)
                                    ->where('futuro', true);
                            })]
                    ]);

                    if ($validator2->fails()) {
                        $inventario = INV_LOT_FUND::select('id', 'semana', 'idlotero', 'idmaterial', 'saldo', 'presente', 'futuro')
                            ->where('semana', $request->semana)
                            ->where('idlotero', $request->idempleado)
                            ->where('idmaterial', $item2->idmaterial)
                            ->where('futuro', true)->first();
                    } else {
                        $inventario = new INV_LOT_FUND();
                        $inventario->semana = $request->semana;
                        $inventario->idlotero = $request->idempleado;
                        $inventario->idmaterial = $item2->idmaterial;
                        $inventario->presente = false;
                        $inventario->futuro = true;
                    }

                    $inventario->saldo = $saldo_fut;

                    $inventario->status = true;
                    $inventario->save();
                }
            endforeach;

        }

        if ($resp) {
            $resp = ['success', 'Guardo con exito!'];
        } else {
            $resp = ['danger', 'Error en el proceso de registro!'];
        }

        return response()->json([
            'code' => $resp ? 200 : 500,
            'status' => $resp ? 'success' : 'danger',
            'reg' => $this->getdespacho($despacho->idempleado, $despacho->semana, $request->hacienda)
        ], 200);
    }

    public function getdespacho($empleado, $semana, $hacienda, $axios = 0)
    {
        $despacho = ENF_EGRESO::select('id', 'fecha', 'idempleado', 'semana', 'idhacienda')
            ->where('idempleado', $empleado)
            ->where('semana', $semana)
            ->where('idhacienda', $hacienda == '343' ? 1 : 2)
            ->where('status', 1)
            ->with(['empleado' => function ($query) use ($semana) {
                $query->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
                $query->with(['lotero' => function ($query3) use ($semana) {
                    $query3->with(['enfunde' => function ($query) {
                        $query->select('id', 'total_pre', 'total_fut', 'idlotero', 'status', 'count');
                    }]);
                    $query3->with(['saldo_semana' => function ($query) use ($semana) {
                        $query->select('idlotero', 'idmaterial', 'semana', 'presente', 'futuro', 'saldo', 'enfunde')
                            ->where('presente', true)
                            ->where('semana', (int)$semana);
                    }]);
                    $query3->with(['saldo_ultima_semana' => function ($query) use ($semana) {
                        $query->select('idlotero', 'idmaterial', 'semana', 'presente', 'futuro', 'saldo', 'enfunde')
                            ->where('futuro', true)
                            ->where('semana', (int)$semana - 1);
                    }]);
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

        //Si es una peticion desde el lado del cliente
        if ($axios && $despacho) {
            $despacho->toArray();
            $despacho->fecha = date("d/m/Y", strtotime($despacho->fecha));
            foreach ($despacho->egresos as $egreso):
                $egreso->fecha = date("d/m/Y", strtotime($egreso->fecha));
            endforeach;
        }

        return $despacho;
    }

    public function deleteDetalle($empleado, $semana, $hacienda, $id)
    {
        //PRESENTE O FUTURO
        $detalle = ENF_DET_EGRESO::find($id);

        $lotero = ENF_LOTERO::select('id', 'idempleado')->where('idempleado', $empleado)->first();

        $inventario = INV_LOT_FUND::select('id', 'semana', 'idlotero', 'idmaterial', 'saldo')
            ->where('semana', $semana)
            ->where('idlotero', $lotero->id)
            ->where('idmaterial', $detalle->idmaterial)
            ->where('presente', $detalle->presente)
            ->where('futuro', $detalle->futuro)
            ->first();

        if ($detalle->presente) {
            $inventario->saldo = $inventario->saldo - $detalle->cantidad;
            $inventario->presente = true;
            $inventario->futuro = false;
        } else {
            if ($detalle->futuro) {
                $inventario->saldo = $inventario->saldo - $detalle->cantidad;
                $inventario->presente = false;
                $inventario->futuro = true;
            }
        }

        if (!$detalle->delete()) {
            return $this->response(500, 'danger', 'No se pudo eliminar');
        } else {

            if (intval($inventario->saldo) == 0) {
                $inventario->delete();
            } else {
                $inventario->save();
            }

            $despacho = $this->getdespacho($empleado, $semana, $hacienda);

            if ($despacho->egresos->isEmpty()) {
                $cabecera = ENF_EGRESO::find($despacho->id);
                $cabecera->delete();

                $inventario = INV_LOT_FUND::select('id', 'semana', 'idlotero')
                    ->where('semana', $semana)
                    ->where('idlotero', $lotero->id);

                $inventario->delete();

            } else {
                //Totaliza detalle
                $totaliza = 0;
                foreach ($despacho->egresos as $egreso):
                    $totaliza += intval($egreso->cantidad);

                endforeach;

                $despacho_cabecera = ENF_EGRESO::select('id', 'total')->find($despacho->id);
                $despacho_cabecera->total = $totaliza;
                $despacho_cabecera->save();
            }
        }

        return $this->respuesta('success', 'Registro eliminado con éxito!');
    }

    public
    function editDetalle(Request $request)
    {
        $detalle = ENF_DET_EGRESO::select('id', 'cantidad')->find($request->id);
        $detalle->cantidad = $request->cantidad;
        $detalle->save();

        $despacho = $this->getdespacho($request->empleado, $request->semana, $request->hacienda);

        //Totaliza detalle
        $totaliza = 0;
        foreach ($despacho->egresos as $egreso):
            $totaliza += intval($egreso->cantidad);
        endforeach;

        $despacho_cabecera = ENF_EGRESO::select('id', 'total')->find($despacho->id);
        $despacho_cabecera->total = $totaliza;
        $despacho_cabecera->save();

        return $this->respuesta('success', 'Editado con exito');
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

    public
    function saldopendiente($idempleado, $semana, $idmaterial)
    {
        $idlotero = ENF_LOTERO::select('id', 'idempleado')->where('idempleado', $idempleado)->first();
        $inventario = INV_LOT_FUND::select('idlotero', 'semana', 'saldo', 'enfunde', 'futuro')
            ->where('idlotero', $idlotero->id)
            ->where('semana', $semana)
            ->where('idmaterial', $idmaterial)
            ->where('futuro', true)->first();

        return $inventario;
    }
}
