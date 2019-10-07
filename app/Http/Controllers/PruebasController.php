<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function cajas()
    {
        return view('empacadora.cajas');
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
