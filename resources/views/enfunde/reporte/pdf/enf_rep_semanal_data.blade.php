<?php $saldo_presente = 0 ?>
<?php $saldo_futuro = 0 ?>
<?php $saldo_desbunche = 0 ?>
<?php $saldo_fundas = 0 ?>
<?php $i = 0 ?>
<?php $temp_array = array(); ?>

<style>
    .rojo {
        background-color: red;
    }

    .cafe {
        background-color: #715000;
    }

    .negro {
        background-color: #000000;
    }

    .verde {
        background-color: #2c8c30;
    }

    .azul {
        background-color: #24447f;
    }

    .blanco {
        background-color: #ffffff;
    }

    .amarillo {
        background-color: #e1df29;
    }

    .lila {
        background-color: #5d29e1;
    }

    table, th, td {
        font-size: 8px;
    }

    td {
        padding: 0px;
        text-align: center;
    }

    th {
        background-color: #94ffe0;
    }
</style>

@if($loteros)
    @foreach($loteros as $lotero)
        <table class="table table-hover" cellspacing="0" cellpadding="0" border="0" style="margin-top: 5rem">
            <tr>
                <th colspan="6" style="background-color: #dcdcdc">
                    {{$lotero->nombres}}</th>
                <th colspan="5" style="text-align: center; background-color: #dcdcdc;"></th>
            </tr>
            @if($lotero->enfunde)
                @foreach($lotero->enfunde->detalle as $enfunde)
                    <tr style="text-align: center">
                        <th colspan="6" style="text-align: center"></th>
                        <td>{{$enfunde->seccion->lote->lote}}</td>
                        <td>{{round($enfunde->seccion->has,2)}}</td>
                        <td style="text-align: center">{{$enfunde->cant_presente}}&nbsp;</td>
                        <td style="text-align: center;">{{$enfunde->cant_futuro}} &nbsp;</td>
                        <td style="text-align: center">{{$enfunde->desbunchado}} &nbsp;</td>
                    </tr>
                @endforeach
                <tr style="text-align: center;">
                    <th colspan="6" style="text-align: center"></th>
                    <th colspan="2"><b>Total Enfunde</b></th>
                    <td style="text-align: center;"><b>{{$lotero->enfunde->total_pre}}&nbsp;</b></td>
                    <td style="text-align: center;"><b>{{$lotero->enfunde->total_fut}} &nbsp;</b></td>
                    <td style="text-align: center;"><b>{{$lotero->enfunde->chapeo}} &nbsp;</b></td>
                </tr>
                <?php $saldo_presente += intval($lotero->enfunde->total_pre)?>
                <?php $saldo_futuro += intval($lotero->enfunde->total_fut)?>
                <?php $saldo_desbunche += intval($lotero->enfunde->chapeo)?>
            @else
                <tr>
                    <th colspan="11" style="text-align: center">No tiene dato de enfunde</th>
                </tr>
            @endif
            @if($lotero->saldos_semana)
                @foreach($lotero->saldos_semana as $saldo)
                    @if(intval($saldo->saldo) > 0)
                        <tr style="text-align: center;">
                            <td colspan="5" style="background-color: #baffe9; text-align: left"><b>&nbsp;&nbsp;
                                Saldo => {{$lotero->nombre_1 . ' ' . $lotero->apellido_1}} |  Material:</b></td>
                            <th colspan="5" style="text-align: right; background-color: #baffe9">
                                <b>{{$saldo->material->nombre}} = </b></th>
                            <td style="text-align: left;background-color: #baffe9"><b> ({{$saldo->saldo}}) &nbsp;</b>
                            </td>
                        </tr>
                        <?php $saldo_fundas += intval($saldo->saldo)?>
                    @endif
                @endforeach
                <?php
                foreach ($lotero->saldos_semana as $val) {
                    if (!in_array($val['material'], $temp_array)) {
                        $temp_array[$i] = $val['material'];
                    }
                    $i++;
                }
                ?>
            @endif
        </table>
    @endforeach
    <table border="0">
        <tr style="text-align: center">
            <th colspan="8" rowspan="6" style="text-align: left; font-size: 10px"><b>ENFUNDE TOTAL</b></th>
            <th style="font-size: 10px"><b><?= number_format($saldo_presente)?></b></th>
            <th style="font-size: 10px"><b><?= number_format($saldo_futuro)?></b></th>
            <th style="font-size: 10px"><b><?= number_format($saldo_desbunche)?> </b></th>
        </tr>
    </table>
    @if(count($temp_array)>0)
        <table>
            <tr>
                <th colspan="10"></th>
            </tr>
            <?php $total_despacho = 0;?>
            @foreach($temp_array as $array)
                <?php $total = 0;?>
                @foreach($loteros as $lotero)
                    @foreach($lotero->fundas->egresos as $despachos)
                        @if($despachos->get_material->id_fila == $array->id_fila)
                            <?php $total = $total + intval($despachos->cantidad)?>
                            <?php $total_despacho += intval($despachos->cantidad)?>
                        @endif
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="9" style="text-align: right; font-size: 9px"><b>DESPACHO {{$array->nombre}}: </b></td>
                    <td colspan="1" style="text-align: right; font-size: 9px"><?= number_format($total)?></td>
                </tr>
                {{$array->total}}
            @endforeach
            <tr>
                <td colspan="9" style="text-align: right; font-size: 10px;background-color: #94ffe0"><b>TOTAL FUNDAS
                        DESPACHADAS: </b></td>
                <td colspan="1"
                    style="text-align: right; font-size: 10px;background-color: #dcdcdc"><?= number_format($total_despacho)?></td>
            </tr>
        </table>
    @endif
    <table>
        @foreach($temp_array as $array)
            <?php $total = 0;?>
            @foreach($loteros as $lotero)
                @foreach($lotero->saldos_semana as $saldo)
                    @if($saldo->material->id_fila == $array->id_fila)
                        <?php $total = $total + intval($saldo->saldo)?>
                    @endif
                @endforeach
            @endforeach
            <tr>
                <td colspan="9" style="text-align: right; font-size: 9px"><b>SALDO {{$array->nombre}}: </b></td>
                <td colspan="1" style="text-align: right; font-size: 9px"><?= number_format($total)?></td>
            </tr>
            {{$array->total}}
        @endforeach
        <tr>
            <td colspan="9" style="text-align: right; font-size: 10px;background-color: #94ffe0"><b>TOTAL FUNDAS
                    SALDO: </b>
            </td>
            <td colspan="1"
                style="text-align: right; font-size: 10px;background-color: #dcdcdc"><?= number_format($saldo_fundas)?></td>
        </tr>
    </table>
    <table border="0">
        <tr>
            <td colspan="9" style="text-align: right; font-size: 10px;background-color: #dcdcdc"><b>TOTAL ENFUNDE: </b>
            </td>
            <td colspan="1"
                style="text-align: right; font-size: 10px;background-color: #dcdcdc"><?= (number_format($saldo_presente + $saldo_futuro))?></td>
        </tr>
    </table>
@endif
