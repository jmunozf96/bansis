<?php

namespace App\Http\Controllers;

use App\Sisban\Enfunde\ENF_DET_EGRESO;
use App\Sisban\Enfunde\ENF_EGRESO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EgresoController extends Controller
{
    public function save(Request $request)
    {
        $resp = false;
        $despacho = new ENF_EGRESO();

        $despacho->fecha = $request->fecha;
        $despacho->semana = $request->semana;
        $despacho->idhacienda = $request->hacienda == '343' ? 1 : 2;
        $despacho->idempleado = $request->idempleado;
        $despacho->total = $request->total;
        $despacho->saldo = $request->saldo;
        $despacho->status = 1;

        $resp = $despacho->save();

        if ($resp) {
            foreach ($request->despachos as $key => $item):
                $item = (object)$item;
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
            endforeach;
        }

        if ($resp) {
            $resp = $this->response(200, 'success', 'Guardo con exito!');
        } else {
            $resp = $this->response(500, 'danger', 'Error en el proceso de registro!');
        }

        return response($resp);
    }

    public function getdespacho($empleado, $semana, $hacienda, $axios = 0)
    {
        $despacho = ENF_EGRESO::select('id', 'fecha', 'idempleado')
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
            }
        }

        return $this->response(200, 'success', 'Registro eliminado de la BD');
    }

    public function response($code, $status, $message)
    {
        return [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ];
    }
}
