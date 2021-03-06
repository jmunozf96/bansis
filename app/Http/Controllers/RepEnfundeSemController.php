<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\Sisban\Enfunde\ENF_ENFUNDE;
use App\Sisban\Enfunde\ENF_LOTERO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use PDF;

class RepEnfundeSemController extends Controller
{
    protected $perfil;
    protected $utilidades;
    protected $recursos;

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware(
            'AccesoURL',
            ['only' => ['index']]
        );
        date_default_timezone_set('America/Guayaquil');
        $this->perfil = new PerfilController();
        $this->utilidades = new UtilidadesController();
    }

    public function index($objeto, $modulo)
    {
        $hacienda_auth = Auth::user()->idHacienda;
        $hacienda = $hacienda_auth == 0 || $hacienda_auth == 1 ? 1 : 2;
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);

        $enfunde_semana = ENF_ENFUNDE::selectRaw('semana, periodo, idhacienda, cinta_pre, cinta_fut, sum(total_pre) as presente, sum(total_fut) as futuro, (sum(total_pre)+sum(total_fut)) as enfunde')
            ->where(['idhacienda' => $hacienda])
            ->distinct('semana')
            ->groupBy('semana', 'periodo', 'idhacienda', 'cinta_pre', 'cinta_fut')
            ->orderBy('cinta_pre', 'desc')
            ->paginate(10);

        if (view()->exists('enfunde.reporte' . '.' . $objeto)) {
            return view('enfunde.reporte' . '.' . $objeto, [
                'recursos' => $recursos,
                'semana' => $this->utilidades->getSemana(),
                'combosemanas' => $this->utilidades->comboSemanas(),
                'enfunde_semanal' => $enfunde_semana
            ])->withInput(Input::all());
        } else {
            return redirect('/');
        }
    }

    public function repIndexEnfundeSemanal(Request $request)
    {
        //return $enfunde_semana;
    }

    //REPORTES
    public function repEnfundeSemanal(Request $request)
    {
        try {
            $json = $request->input('json');
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            $html = '';


            if (!empty($params_array) && count($params_array) > 0) {

                //SILVIO SOLORZANO ID ENFUNDE 5 -> SE GUARDARON LOS LOTES DOS VECES
                $validation = \Illuminate\Support\Facades\Validator::make($params_array, [
                    'semana' => 'required',
                    'hacienda' => 'required',
                    'color_pre' => 'required',
                    'color_fut' => 'required'
                ]);

                if (!$validation->fails()) {
                    $enfunde_semana_loteros = ENF_ENFUNDE::where([
                        'idhacienda' => $params_array['hacienda'],
                        'semana' => $params_array['semana'],
                    ])->get(['idlotero']);

                    $loteros = ENF_LOTERO::on('sqlsrv')
                        ->where([
                            'idhacienda' => $params_array['hacienda'],
                        ])
                        ->whereIn('id', $enfunde_semana_loteros)
                        ->with(['enfunde' => function ($query) use ($params_array) {
                            $query->select('id', 'idlotero', 'fecha', 'total_pre', 'total_fut', 'chapeo', 'cinta_pre', 'cinta_fut');
                            $query->with(['detalle' => function ($query) {
                                $query->select('id', 'idenfunde', 'idseccion', 'cant_presente', 'cant_futuro', 'desbunchado', 'presente', 'futuro');
                                $query->with(['seccion' => function ($query) {
                                    $query->select('id', 'idlote', 'has');
                                    $query->with(['lote' => function ($query) {
                                        $query->select('id', 'lote', 'has', 'variedad');
                                    }]);
                                }]);
                            }]);
                            $query->where([
                                'semana' => $params_array['semana']
                            ]);
                        }])
                        ->with(['saldos_semana' => function ($query) use ($params_array) {
                            $query->select('idlotero', 'semana', 'idmaterial', 'saldo_inicial', 'entrada', 'salida', 'saldo', 'status');
                            $query->with(['material' => function ($query) {
                                $query->select('id_fila', 'nombre');
                            }]);
                            $query->where([
                                'semana' => $params_array['semana']
                            ]);
                        }])
                        ->with(['fundas' => function ($query) use ($params_array) {
                            $query->select('id', 'semana', 'idempleado', 'total', 'status');
                            $query->with(['egresos' => function ($query) {
                                $query->select('id', 'id_egreso', 'fecha', 'idmaterial', 'idempleado', 'cantidad', 'presente', 'futuro');
                                $query->with(['get_material' => function ($query) {
                                    $query->select('id_fila', 'nombre');
                                }]);
                                $query->with(['nom_reemplazo' => function ($query) {
                                    $query->selectRaw('COD_TRABAJ, trim(NOMBRE_CORTO) as nombre');
                                }]);
                            }]);
                            $query->where([
                                'semana' => $params_array['semana']
                            ]);
                        }])
                        ->orderBy('nombres')
                        ->get();

                    if (count($loteros) > 0) {
                        $semana_color = [
                            'presente' => $params_array['color_pre'],
                            'futuro' => $params_array['color_fut'],
                        ];

                        //return $enfunde_semana_loteros;

                        $html = view('enfunde.reporte.pdf.enf_rep_semanal_data', compact('loteros', 'semana_color'));

                        // Custom Header
                        PDF::setHeaderCallback(function ($pdf) use ($loteros, $params_array) {
                            $semana_color = [
                                'presente' => $params_array['color_pre'],
                                'futuro' => $params_array['color_fut'],
                            ];

                            $hacienda = $params_array['hacienda'] == 0 || $params_array['hacienda'] == 1 ? 'PRIMOBANANO' : 'SOFCABANANO';

                            $pdf_head_subtitle = 'Usuario: ' . Auth::user()->Nombre . "<br>Fecha de creación: " . Date('d/m/Y') .
                                "<br><b>REPORTE DE ENFUNDE - {$hacienda}</b> | Enfunde correspondiente a la semana {$params_array['semana']}<hr>";
                            // Set font
                            $pdf->SetFont('helvetica', 'B', 10);
                            // Title
                            $tbl = view('enfunde.reporte.pdf.enf_rep_cabecera', compact('pdf_head_subtitle', 'semana_color'));
                            $pdf->writeHTML($tbl, true, false, false, false, '');
                        });

                        // Custom Footer
                        PDF::setFooterCallback(function ($pdf) {

                            // Position at 15 mm from bottom
                            $pdf->SetY(-10);
                            // Set font
                            $pdf->SetFont('helvetica', 'I', 8);
                            // Page number
                            $pdf->Cell(0, 10, 'Página ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
                        });

                        PDF::SetMargins(PDF_MARGIN_LEFT, 22.5, PDF_MARGIN_RIGHT);
                        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
                        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

                        //return abort(500);
                        PDF::AddPage('P', 'A4');
                        PDF::SetTitle('Enfunde Semanal');

                        PDF::writeHTML($html, true, false, false, false, '');

                        PDF::Output('Enfunde_semana_' . $params_array['semana'] . '.pdf');
                        exit;
                    } else {
                        return abort(404);
                    }
                } else {
                    return abort(404);
                }
            } else {
                return abort(500, 'No se recibieron los parametros establecidos de enfunde');
            }
        } catch (\Exception $ex) {
            return abort(500, $ex->getMessage());
        }
    }
}
