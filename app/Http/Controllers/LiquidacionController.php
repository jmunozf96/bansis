<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

class LiquidacionController extends Controller
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

    public function index($objeto, $recursos)
    {
        $this->recursos = $recursos;
        $hacienda = Auth::user()->idHacienda == 0 ? 1 : Auth::user()->idHacienda;

        if (view()->exists('produccion' . '.' . $objeto)) {
            return view('produccion' . '.' . $objeto, [
                'recursos' => $this->recursos,
                'semana' => $this->utilidades->getSemana(),
            ])->withInput(Input::all());
        } else {
            return redirect('/');
        }
    }

    public function uploadFile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'hacienda' => 'required',
            'semana' => 'required|numeric|min:1|max:52',
            'archivo' => 'required|mimes:html',
        ]);

        if (!$validator->fails()) {
            $html = $request->file('archivo');
            $rename = $html->getClientOriginalName();

            $upload = $html->storeAs('produccion/liquidacion', $rename);


            $html_server = File::get(storage_path('app/produccion/liquidacion/' . $rename));
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML($html_server);


            $liquidacion = new \stdClass();
            $liquidacion->numero = explode(" ", $dom->getElementsByTagName('table')[1]->childNodes[1]->childNodes[2]->nodeValue)[2];
            $liquidacion->codFinca = trim($dom->getElementsByTagName('table')[3]->childNodes[1]->childNodes[3]->nodeValue);
            $liquidacion->nomFinca = trim($dom->getElementsByTagName('table')[3]->childNodes[1]->childNodes[5]->nodeValue);
            $liquidacion->rucFinda = trim($dom->getElementsByTagName('table')[3]->childNodes[1]->childNodes[9]->nodeValue);
            $liquidacion->fecha = trim($dom->getElementsByTagName('table')[3]->childNodes[5]->childNodes[5]->nodeValue);

            $tabla_data = array();
            $detalle_data = array();
            $posicion = 0;
            $detalle = $dom->getElementsByTagName('table')[5];
            $detalle = $detalle->childNodes;
            foreach ($detalle as $key => $tabla):
                $tbody = $tabla->childNodes;
                $objetos = array();
                if (count($tbody) > 0) {
                    foreach ($tbody as $key2 => $cuerpo) :
                        $tr = trim(str_replace("&nbsp;", '', htmlentities($cuerpo->nodeValue)));
                        if (!empty($tr))
                            array_push($objetos, $tr);
                    endforeach;
                    if (count($objetos) == 3) {
                        if (count($detalle_data) > 0) {
                            $tabla_data[$posicion]['Detalle'] = $detalle_data;
                            $posicion++;
                            $detalle_data = array();
                        }
                        array_push($tabla_data, $objetos);
                    } elseif (count($objetos) == 7) {
                        array_push($detalle_data, $objetos);
                    } elseif (count($objetos) != 7 && count($objetos) != 3) {
                        if (!empty($detalle_data)) {
                            $tabla_data[$posicion]['Detalle'] = $detalle_data;
                            $posicion++;
                            $detalle_data = array();
                        }
                    }
                }
            endforeach;

            unset($tabla_data[count($tabla_data) - 1]);

            //Bonos
            foreach ($tabla_data as $key => $detalle) {
                $cajas = $detalle['Detalle'];
                $bono = array();
                foreach ($cajas as $key1 => $caja) {
                    $idbono = strtolower(str_replace(" ", "", $caja[0]));
                    if ($idbono == 'bonodeempaque') {
                        $cod_caja = trim($caja[2]);
                        $cantidad = trim($caja[4]);
                        $tarifa = trim($caja[5]);

                        $caja_bono = [
                            'cod_caja' => $cod_caja,
                            'cantidad' => $cantidad,
                            'tarifa' => $tarifa
                        ];

                        array_push($bono, $caja_bono);

                    }
                }
                if (count($bono) > 0) {
                    foreach ($cajas as $key2 => $caja) {
                        $idbono = strtolower(str_replace(" ", "", $caja[0]));
                        $cod_caja = trim($caja[2]);
                        $cantidad = intval(trim($caja[4]));
                        foreach ($bono as $key3 => $add_bono) {
                            if ($add_bono['cod_caja'] == $cod_caja && $idbono != 'bonodeempaque' && $cantidad == intval($add_bono['cantidad'])) {
                                $tabla_data[$key]['Detalle'][$key2][7] = $bono[$key3];
                            }
                        }
                    }
                }

            }

            $liquidacion->cajas = $tabla_data;
            $data = [
                'code' => 202,
                'message' => 'Se cargo la informacion correctamente',
                'status' => 'success',
                'semana' => $request->input('semana'),
                'liquidacion' => $liquidacion
            ];
            /*return response()->json($liquidacion, 200);
            die;*/
            return redirect()->back()->with($data)->withInput(Input::all());
        } else {
            return redirect()->back()->withInput(Input::all())->withErrors($validator);
        }
    }

    public function getCajasSemana(Request $request)
    {

        $json = $request->input('json');
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $validation = \Validator::make($params_array, [
            'semana' => 'required',
            'hacienda' => 'required',
            'tipo_caja' => 'required'
        ]);

        if (!$validation->fails()) {
            $liquidacion_cajas = DB::connection('bansis')
                ->table('PRD_DET_DISTRIBUCION as distribucion')
                ->join('CAJ_CODIGOS_COLE as cod_dole', 'cod_dole.id_cjubesa', '=', 'distribucion.id_cjubesa')
                ->join('PRD_DET_PRODUCCION_DIARIA as detalle', 'distribucion.id_det_prod', '=', 'detalle.id_detprod')
                ->join('PRD_PRODUCCION_DIARIA as produccion', 'detalle.id_prod', '=', 'produccion.id_prod')
                ->select([
                    'produccion.sem_prod',
                    'produccion.per_prod',
                    'produccion.id_hacienda',
                    'produccion.fecha_prod',
                    'detalle.des_detprod',
                    'detalle.cant_prod',
                    'detalle.ptotal_detprod',
                    'cod_dole.ds_cjubes',
                    'distribucion.des_distrib',
                    'distribucion.cant_distrib',
                    'distribucion.cod_trans_distrib'

                ])
                ->where([
                    'produccion.id_hacienda' => $params_array['hacienda'],
                    'produccion.sem_prod' => $params_array['semana'],
                    'cod_dole.ds_cjubes' => $params_array['tipo_caja']
                ])
                ->get();

            return response()->json([
                'code' => 202,
                'liquidacion_semana' => $liquidacion_cajas
            ], 202);
        }

        return 'hola';
    }

}
