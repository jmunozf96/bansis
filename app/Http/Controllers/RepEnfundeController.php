<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\Sisban\Enfunde\ENF_ENFUNDE;
use App\Sisban\Enfunde\ENF_LOTERO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\Cast\Object_;
use PDF;

class RepEnfundeController extends Controller
{
    protected $perfil;
    protected $utilidades;
    protected $recursos;

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('AccesoURL',
            ['only' => ['index']]);
        date_default_timezone_set('America/Guayaquil');
        $this->perfil = new PerfilController();
        $this->utilidades = new UtilidadesController();
    }

    public function index($objeto, $recursos)
    {
        $this->recursos = $recursos;
        $hacienda = Auth::user()->idHacienda == 0 ? 1 : Auth::user()->idHacienda;
        //return response()->json($this->comboLoteros($hacienda), 200);
        if (view()->exists('enfunde.reporte' . '.' . $objeto)) {
            return view('enfunde.reporte' . '.' . $objeto, [
                'recursos' => $this->recursos,
                'semana' => $this->utilidades->getSemana(),
                'combosemanas' => $this->comboSemanas(),
                'comboloteros' => $this->comboLoteros($hacienda)
            ])->withInput(Input::all());
        } else {
            return redirect('/');
        }
    }

    public function getEnfunde()
    {
        $params = Input::all();
        $enfunde = ENF_ENFUNDE::query();

        //$enfunde = $enfunde->select('id','semana','periodo','cinta_pre','cinta_fut','idlotero');
        if (!is_null($params['hacienda']) || !empty($params['hacienda'])):
            $enfunde = $enfunde->where('idhacienda', $params['hacienda']);
        endif;

        if (!is_null($params['semana']) || !empty($params['semana'])):
            $enfunde = $enfunde->where('semana', $params['semana']);
        endif;

        if (is_null($params['lotero'][0])) {
            unset($params['lotero'][0]);
        }

        if (count($params['lotero']) > 0):
            $enfunde = $enfunde->wherein('idlotero', $params['lotero']);
        endif;

        $enfunde = $enfunde->with(['lotero' => function ($query) {
            $query->select('id', 'nombres');
        }]);;

        $enfunde = $enfunde->get();

        //return response()->json($enfunde, 200);
        return Redirect::back()
            ->with([
                'data_enfunde' => $enfunde
            ]);
    }

    public function comboSemanas()
    {
        $periodo = array();
        $periodo[''] = 'Todas las semanas';

        $c = 1;
        for ($x = 1; $x <= 13; $x++) {
            $semana = array();
            for ($y = 1; $y <= 4; $y++) {
                $semana[$c] = "Semana $c";
                $c++;
            }
            $periodo["Periodo $x"] = $semana;
        }

        return (Object)$periodo;
    }

    public function comboLoteros($hacienda)
    {
        $comboLoteros = array();
        $comboLoteros[''] = 'Todos los loteros';
        $loteros = ENF_LOTERO::query();

        $loteros = $loteros->select('id', 'nombres');

        if (!is_null($hacienda) && !empty($hacienda)) {
            if ($hacienda == 1 || $hacienda == 2) {
                $loteros = $loteros->where(['idhacienda' => $hacienda]);
            }
        }

        $loteros = $loteros->get();
        foreach ($loteros as $lotero) {
            $comboLoteros[$lotero->id] = $lotero->nombres;
        }

        return (Object)$comboLoteros;
    }

    //REPORTES


    public function repEnfundeSemanal()
    {
        PDF::SetTitle('Enfunde Semanal');
        PDF::AddPage('P','A4');

        $loteros = ENF_LOTERO::where(['idhacienda' => 1])
            ->with(['enfunde' => function ($query) {
                $query->select('id', 'idlotero', 'total_pre', 'total_fut', 'chapeo', 'cinta_pre', 'cinta_fut');
                $query->with(['detalle' => function ($query) {
                    $query->select('id', 'idenfunde', 'idseccion', 'cantidad', 'desbunchado', 'presente', 'futuro');
                    $query->with(['seccion' => function ($query) {
                        $query->select('id', 'idlote', 'has');
                        $query->with(['lote' => function ($query) {
                            $query->select('id', 'lote', 'has', 'variedad');
                        }]);
                    }]);
                }]);
                $query->where([
                    'semana' => 47
                ]);
            }])
            ->get();


        //return $loteros;

        $html = view('enfunde.reporte.pdf.enf_rep_semanal_data', compact('loteros'));

        PDF::writeHTML($html, true, false, false, false, '');

        PDF::Output('hello_world.pdf');
    }
}
