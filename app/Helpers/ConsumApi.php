<?php
/**
 * Created by PhpStorm.
 * User: Estadisticas
 * Date: 24/09/2019
 * Time: 15:01
 */

namespace App\Helpers;

class ConsumApi
{
    public function getProduccion($hacienda, $datefrom, $dateuntil, $access_token)
    {
        $url = $hacienda . '&' . $datefrom . '&' . $dateuntil . '&' . $access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data_out = curl_exec($ch);
        curl_close($ch);
        echo $data_out;
    }
}