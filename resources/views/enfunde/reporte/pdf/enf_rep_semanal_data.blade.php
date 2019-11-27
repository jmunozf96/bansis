<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    td {
        padding: 3px;
        text-align: center;
    }
</style>
@foreach($loteros as $lotero)
    <table class="table table-hover" cellspacing="0" cellpadding="0" border="1">
        <tr>
            <th colspan="5">
                {{$lotero->apellido_1 . ' ' . $lotero->nombre_1 . ' ' . $lotero->nombre_2}}</th>
            <th colspan="5"><b></b></th>
        </tr>
        <tr style="text-align: center">
            <th colspan="5" rowspan="5" style="text-align: center"></th>
            <th><b>Lote</b></th>
            <th><b>has</b></th>
            <th><b>Pre.</b></th>
            <th><b>Fut.</b></th>
            <th><b>Desb.</b></th>
        </tr>
        @foreach($lotero->enfunde->detalle as $enfunde)
            @if($enfunde->presente)
                <tr style="text-align: center">
                    <td>{{$enfunde->seccion->lote->lote}}</td>
                    <td>{{round($enfunde->seccion->has,2)}}</td>
                    <td>{{$enfunde->presente ? $enfunde->cantidad : 0}}</td>
                    @foreach($lotero->enfunde->detalle as $enfunde2)
                        @if($enfunde2->futuro && $enfunde->idseccion == $enfunde2->idseccion)
                            <td>{{$enfunde2->cantidad}}</td>
                            <td>{{$enfunde2->desbunchado}}</td>
                        @endif
                    @endforeach
                </tr>
            @endif
        @endforeach
    </table>
@endforeach