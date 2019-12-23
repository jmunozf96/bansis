<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use App\Http\Controllers\Sistema\UtilidadesController;
use App\Sisban\Enfunde\ENF_EGRESO;
use App\Sisban\Enfunde\ENF_ENFUNDE;
use App\Sisban\Enfunde\ENF_LOTERO;
use App\Sisban\Enfunde\INV_LOT_FUND;
use App\XASS_InvBodegas;
use App\XASS_InvProductos;
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
                'combosemanas' => $this->utilidades->comboSemanas(),
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
        if (isset($params['hacienda']) && !is_null($params['hacienda']) || !empty($params['hacienda'])):
            $enfunde = $enfunde->where('idhacienda', $params['hacienda']);
        endif;

        if (isset($params['semana']) && !is_null($params['semana']) || !empty($params['semana'])):
            $enfunde = $enfunde->where('semana', $params['semana']);
        endif;

        if (isset($params['lotero'])):
            if (is_null($params['lotero'][0])):
                unset($params['lotero'][0]);
            else:
                $enfunde = $enfunde->wherein('idlotero', $params['lotero']);
            endif;
        endif;

        $enfunde = $enfunde->with(['lotero' => function ($query) {
            $query->select('id', 'nombres');
        }]);

        $enfunde = $enfunde->get();


        return Redirect::back()
            ->with([
                'data_enfunde' => $enfunde
            ])
            ->withInput(Input::all());
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


}
