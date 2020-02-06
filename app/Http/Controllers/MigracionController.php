<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\toJSON;
use  XBase\Table;

class MigracionController extends Controller
{
    public function index($hacienda, $fecha)
    {
        $des_hacienda = $hacienda == 1 ? 'PRIMOBANANO' : 'SOFCABANANO';

        echo "<h1>$des_hacienda</h1>";
        //Cargamos liquidacion
        $resp = $this->liquidacion($hacienda, $fecha);
        if ($resp) {
            echo 'Liquidacion cargada. <hr>';
        } else {
            echo 'Error al cargar liquidacion, datos ya existen. <hr>';
        }

        //Cargamos cosecha
        $resp = $this->cosecha($hacienda, $fecha);
        if ($resp) {
            echo 'Cosecha cargada. <hr>';
        } else {
            echo 'Error al cargar Cosecha, datos ya existen. <hr>';
        }

        //Cargamos muestreo
        $resp = $this->muestreo($hacienda, $fecha);
        if ($resp) {
            echo 'Muestreo cargada. <hr>';
        } else {
            echo 'Error al cargar Muestreo, datos ya existen. <hr>';
        }

    }

    public function liquidacion($hacienda, $fecha)
    {
        $tabla_referencia = 'liquid_primo';

        if ($hacienda == 1) {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/primo/' . $fecha . '/lq' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
        } else {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/sofca/' . $fecha . '/lq' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
            $tabla_referencia = 'liquid_sofca';
        }

        DB::beginTransaction();

        $resp = false;


        while ($record = $table->nextRecord()) {

            $query = "select top 1 lq_fecha from " . $tabla_referencia . " where lq_fecha = '" . date('Y-m-d', strtotime($record->lq_fecha)) . "' order by lq_fecha desc";
            $existe = DB::connection('sisban')
                ->select($query);

            if (!$existe || $resp) {
                $liquidacion = new \stdClass();
                $liquidacion->lq_haciend = $record->lq_haciend;
                $liquidacion->lq_fecha = date('d/m/Y', strtotime($record->lq_fecha));
                $liquidacion->lq_hora = $record->lq_hora;
                $liquidacion->lq_corter = $record->lq_corter;
                $liquidacion->lq_arrum = $record->lq_arrum;
                $liquidacion->lq_garru = $record->lq_garru;
                $liquidacion->lq_horasc = $record->lq_horasc;
                $liquidacion->lq_area = $record->lq_area;
                $liquidacion->lq_areaefe = $record->lq_areaefe;
                $liquidacion->lq_areapcn = $record->lq_areapcn;
                $liquidacion->lq_inicio = $record->lq_inicio;
                $liquidacion->lq_fin = $record->lq_fin;
                $liquidacion->lq_hombres = $record->lq_hombres;
                $liquidacion->lq_embalad = $record->lq_embalad;
                $liquidacion->lq_tpcomid = $record->lq_tpcomid;
                $liquidacion->lq_tpmater = $record->lq_tpmater;
                $liquidacion->lq_tpffrut = $record->lq_tpffrut;
                $liquidacion->lq_tpmecan = $record->lq_tpmecan;
                $liquidacion->lq_tpotros = $record->lq_tpotros;
                $liquidacion->lq_seccion = $record->lq_seccion;
                $liquidacion->lq_observ = $record->lq_observ;
                $liquidacion->lq________ = ' '; //No se puede introducir valores nulos
                $liquidacion->lq_color1 = $record->lq_color1;
                $liquidacion->lq_color2 = $record->lq_color2;
                $liquidacion->lq_color3 = $record->lq_color3;
                $liquidacion->lq_color4 = $record->lq_color4;
                $liquidacion->lq_color5 = $record->lq_color5;
                $liquidacion->lq_color6 = $record->lq_color6;
                $liquidacion->lq_racim1 = $record->lq_racim1;
                $liquidacion->lq_racim2 = $record->lq_racim2;
                $liquidacion->lq_racim3 = $record->lq_racim3;
                $liquidacion->lq_racim4 = $record->lq_racim4;
                $liquidacion->lq_racim5 = $record->lq_racim5;
                $liquidacion->lq_racim6 = $record->lq_racim6;
                $liquidacion->lq_racimos = $record->lq_racimos;
                $liquidacion->lq_recusad = $record->lq_recusad;
                $liquidacion->lq_proces = $record->lq_proces;
                $liquidacion->lq_rhhomb = $record->lq_rhhomb;
                $liquidacion->lq_rhcort = $record->lq_rhcort;
                $liquidacion->lq_peso = $record->lq_peso;
                $liquidacion->lq_sumpeso = $record->lq_sumpeso;
                $liquidacion->lq_sumrecu = $record->lq_sumrecu;
                $liquidacion->lq_sumproc = $record->lq_sumproc;
                $liquidacion->lq_sumemp = $record->lq_sumemp;
                $liquidacion->lq_ratio = $record->lq_ratio;
                $liquidacion->lq_ratiopc = $record->lq_ratiopc;
                $liquidacion->lq_merma = $record->lq_merma;
                $liquidacion->lq_mermapc = $record->lq_mermapc;
                $liquidacion->lq_calib2 = $record->lq_calib2;
                $liquidacion->lq_manos = $record->lq_manos;
                $liquidacion->lq_cajas = $record->lq_cajas;
                $liquidacion->lq_horase = $record->lq_horase;
                $liquidacion->lq_chemp = $record->lq_chemp;
                $liquidacion->lq_chhomb = $record->lq_chhomb;
                $liquidacion->lq_chembal = $record->lq_chembal;

                if ($resp = $this->detalleLiquidacion($hacienda, $fecha)) {
                    DB::commit();
                    $resp = DB::connection('sisban')->table($tabla_referencia)->insert((array)$liquidacion);
                } else {
                    DB::rollBack();
                    return $resp;
                }
            } else {
                DB::rollBack();
                return $resp;
            }
        }

        return $resp;
    }

    public function detalleLiquidacion($hacienda, $fecha)
    {
        $tabla_referencia = 'lqdet1_primo';

        if ($hacienda == 1) {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/primo/' . $fecha . '/l1' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
        } else {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/sofca/' . $fecha . '/l1' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
            $tabla_referencia = 'lqdet1_sofca';
        }

        $resp = false;

        while ($record = $table->nextRecord()) {

            $query = "select top 1 l1_fecha from " . $tabla_referencia . " where l1_fecha = '" . date('Y-m-d', strtotime($record->l1_fecha)) . "' order by l1_fecha desc";
            $existe = DB::connection('sisban')
                ->select($query);

            if (!$existe || $resp) {
                $ldetalle = new \stdClass();
                $ldetalle->l1_haciend = $record->l1_haciend;
                $ldetalle->l1_fecha = date('d/m/Y', strtotime($record->l1_fecha));
                $ldetalle->l1_caja = $record->l1_caja;
                $ldetalle->l1_cant = $record->l1_cant;
                $ldetalle->l1_libras = $record->l1_libras;

                $resp = DB::connection('sisban')->table($tabla_referencia)->insert((array)$ldetalle);
            } else {
                return false;
            }
        }

        return true;
    }

    public function cosecha($hacienda, $fecha)
    {
        $tabla_referencia = 'cosecha_primo';

        if ($hacienda == 1) {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/primo/' . $fecha . '/cs' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
        } else {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/sofca/' . $fecha . '/cs' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
            $tabla_referencia = 'cosecha_sofca';
        }

        $resp = false;

        while ($record = $table->nextRecord()) {

            if (!$resp) {
                $query = "select top 1 cs_fecha from " . $tabla_referencia . " where cs_fecha = '" . date('Y-m-d', strtotime($record->cs_fecha)) . "' order by cs_fecha desc";
                $existe = DB::connection('sisban')
                    ->select($query);
            }

            if (!$existe || $resp) {
                $cosecha = new \stdClass();
                $cosecha->cs_haciend = $record->cs_haciend;
                $cosecha->cs_fecha = date('d/m/Y', strtotime($record->cs_fecha));
                $cosecha->cs_convoy = $record->cs_convoy;
                $cosecha->cs_n = $record->cs_n;
                $cosecha->cs_avance = $record->cs_avance;
                $cosecha->cs_hora = $record->cs_hora;
                $cosecha->cs_tipo = $record->cs_tipo;
                $cosecha->cs_seccion = $record->cs_seccion;
                $cosecha->cs_garru = $record->cs_garru;
                $cosecha->cs_color = $record->cs_color;
                $cosecha->cs_peso = $record->cs_peso;
                $cosecha->cs_dano = $record->cs_dano;
                $cosecha->cs_nivdano = $record->cs_nivdano;

                $resp = DB::connection('sisban')->table($tabla_referencia)->insert((array)$cosecha);
            } else {
                return $resp;
            }
        }

        return $resp;
    }

    public function muestreo($hacienda, $fecha)
    {
        $tabla_referencia = 'muestreo_primo';

        if ($hacienda == 1) {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/primo/' . $fecha . '/mu' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
        } else {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/sofca/' . $fecha . '/mu' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
            $tabla_referencia = 'muestreo_sofca';
        }

        $resp = false;

        while ($record = $table->nextRecord()) {

            if (!$resp) {
                $query = "select top 1 mu_fecha from " . $tabla_referencia . " where mu_fecha = '" . date('Y-m-d', strtotime($record->mu_fecha)) . "' order by mu_fecha desc";
                $existe = DB::connection('sisban')
                    ->select($query);
            }

            if (!$existe || $resp) {
                $muestreo = new \stdClass();
                $muestreo->mu_haciend = $record->mu_haciend;
                $muestreo->mu_fecha = date('d/m/Y', strtotime($record->mu_fecha));
                $muestreo->mu_convoy = $record->mu_convoy;
                $muestreo->mu_n = $record->mu_n;
                $muestreo->mu_hora = $record->mu_hora;
                $muestreo->mu_largo2 = $record->mu_largo2;
                $muestreo->mu_largou = $record->mu_largou;
                $muestreo->mu_calib2 = $record->mu_calib2;
                $muestreo->mu_calibu = $record->mu_calibu;
                $muestreo->mu_manos = $record->mu_manos;
                $muestreo->mu________ = ' ';
                $muestreo->mu_tipo = $record->mu_tipo;
                $muestreo->mu_seccion = $record->mu_seccion;
                $muestreo->mu_garru = $record->mu_garru;
                $muestreo->mu_color = $record->mu_color;
                $muestreo->mu_peso = $record->mu_peso;

                $resp = DB::connection('sisban')->table($tabla_referencia)->insert((array)$muestreo);
            } else {
                return $resp;
            }
        }

        return $resp;
    }

    public function perdidas($hacienda, $fecha)
    {
        $tabla_referencia = 'perdidas_primo';

        if ($hacienda == 1) {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/primo/' . $fecha . '/mu' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
        } else {
            $table = new Table(dirname(str_replace('\\', '/', __FILE__)) . '/tables/sofca/' . $fecha . '/mu' . substr($fecha, 0, 6) . '.dbf', null, 'CP1251');
            $tabla_referencia = 'perdidas_sofca';
        }

        $query = 'select top 1 pe_fecha from ' . $tabla_referencia . ' order by pe_fecha desc';
        $existe = DB::connection('sisban')
            ->select($query);

        $resp = false;

        while ($record = $table->nextRecord()) {
            if (date('d/m/Y', strtotime($existe[0]->pe_fecha)) != date('d/m/Y', strtotime($record->pe_fecha)) || $resp) {
                $caidas = new \stdClass();
                $caidas->pe_haciend = $record->pe_haciend;
                $caidas->pe_fecha = date('d/m/Y', strtotime($record->pe_fecha));
                $caidas->pe_seccion = $record->pe_seccion;
                $caidas->pe_color = $record->pe_color;
                $caidas->pe_dano = $record->pe_dano;
                $caidas->pe_cant = $record->pe_cant;

                $resp = DB::connection('sisban')->table($tabla_referencia)->insert((array)$caidas);
            } else {
                return response()->json('Ya existen datos con esta fecha', 202);
            }
        }

        return response()->json($resp, 202);
    }
}
