<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Perfil\PerfilController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PruebasController extends Controller
{
    protected $perfil;

    public function __construct()
    {
        $this->perfil = new PerfilController();
        $this->middleware('auth');
    }

    public function cajas()
    {
        $recursos = $this->perfil->getRecursos(Auth::user()->ID);
        return view('empacadora.cajas', [
            'recursos' => $recursos
        ]);
    }

    public function getDataApi($hacienda, $datefrom, $dateuntil, $access_token = 'd8f1a9e7-7697-4f92-b07b-20ad743f9112')
    {
        $hacienda = $hacienda == 343 ? 55 : 56;

        $url = 'http://ingreatsol.com/appbalanza/api/web/index.php/caja/listreport/' .
            $hacienda . '?datefrom=' . $datefrom . '&dateuntil=' . $dateuntil . '&access-token=' . $access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data_out = curl_exec($ch);
        curl_close($ch);

        $data_array = json_decode($data_out, true);


        return response()->json([
            'cajas' => $data_array
        ]);
    }
}
