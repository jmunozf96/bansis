<?php

namespace App\Http\Controllers;

use App\Sisban\Enfunde\ENF_DET_EGRESO;
use App\Sisban\Enfunde\ENF_EGRESO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EgresoController extends Controller
{

    function __construct()
    {
        date_default_timezone_set('America/Guayaquil');
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
        }

        if ($resp) {
            $resp = ['success', 'Guardo con exito!'];
        } else {
            $resp = ['danger', 'Error en el proceso de registro!'];
        }

        return $this->respuesta($resp[0], $resp[1]);
    }

    public function getdespacho($empleado, $semana, $hacienda, $axios = 0)
    {
        $despacho = ENF_EGRESO::select('id', 'fecha', 'idempleado', 'semana', 'idhacienda')
            ->where('idempleado', $empleado)
            ->where('semana', $semana)
            ->where('idhacienda', $hacienda == '343' ? 1 : 2)
            ->with(['empleado' => function ($query) {
                $query->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
            }])
            ->with(['egresos' => function ($query) {
                $query->select('id', 'id_egreso', 'fecha', 'idmaterial', 'cantidad', 'presente', 'futuro', 'status');
                $query->with(['get_material' => function ($query1) {
                    $query1->selectRaw('id_fila,rtrim(codigo) codigo,nombre,bodegacompra');
                }]);
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
        $detalle = ENF_DET_EGRESO::find($id);

        if (!$detalle->delete()) {
            return $this->response(500, 'danger', 'No se pudo eliminar');
        } else {
            $despacho = $this->getdespacho($empleado, $semana, $hacienda);

            if ($despacho->egresos->isEmpty()) {
                $cabecera = ENF_EGRESO::find($despacho->id);
                $cabecera->delete();
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

        return $this->respuesta('success', 'Registro eliminado de la BD');
    }

    public function editDetalle(Request $request)
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

    public function respuesta($status, $messagge)
    {
        $data = [
            'status' => $status,
            'message' => $messagge
        ];

        return response()->json($data, 200);
    }
}
