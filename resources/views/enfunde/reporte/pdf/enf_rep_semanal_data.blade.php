<style>
    table, th, td {
        font-size: 8px;
    }

    td {
        padding: 3px;
        text-align: center;
    }

    th {
        background-color: #94ffe0;
    }
</style>
<table border="1">
    <tr style="text-align: center">
        <th colspan="6" rowspan="6" style="text-align: center"><b>Lotero</b></th>
        <th><b>Lote</b></th>
        <th><b>has</b></th>
        <th><b>Pre.</b></th>
        <th><b>Fut.</b></th>
        <th><b>Desb.</b></th>
    </tr>
</table>
@foreach($loteros as $lotero)
    <table class="table table-hover" cellspacing="0" cellpadding="0" border="0" style="margin-top: 5rem">
        <tr>
            <th colspan="6" style="background-color: #dcdcdc">
                {{$lotero->apellido_1 . ' ' . $lotero->nombre_1 . ' ' . $lotero->nombre_2}}</th>
            <th colspan="5" style="text-align: center; background-color: #dcdcdc;">
                <b>{{'Presente:'.$lotero->enfunde->total_pre}}
                    | {{'Futuro:'.$lotero->enfunde->total_fut}}</b></th>
        </tr>
        @foreach($lotero->enfunde->detalle as $enfunde)
            @if($enfunde->presente)
                <tr style="text-align: center">
                    <th colspan="6" style="text-align: center"></th>
                    <td>{{$enfunde->seccion->lote->lote}}</td>
                    <td>{{round($enfunde->seccion->has,2)}}</td>
                    <td style="text-align: right">{{$enfunde->presente ? $enfunde->cantidad : 0}} &nbsp;</td>
                    @foreach($lotero->enfunde->detalle as $enfunde2)
                        @if($enfunde2->futuro && $enfunde->idseccion == $enfunde2->idseccion)
                            <td style="text-align: right;">{{$enfunde2->cantidad}} &nbsp;</td>
                            <td style="text-align: right">{{$enfunde2->desbunchado}} &nbsp;</td>
                        @endif
                    @endforeach
                </tr>
            @endif
        @endforeach
    </table>
@endforeach