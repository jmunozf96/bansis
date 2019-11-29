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
</style>
@inject('utilidades', 'App\Http\Controllers\Sistema\UtilidadesController')
{!! $pdf_head_subtitle !!}
<table border="0">
    <tr style="text-align: center">
        <th colspan="6" rowspan="6" style="text-align: center"><b>Lotero</b></th>
        <th><b>Lote</b></th>
        <th><b>has</b></th>
        <th class="{{$semana_color['presente']}}"><b></b></th>
        <th class="{{$semana_color['futuro']}}"><b></b></th>
        <th><b>Desb.</b></th>
    </tr>
</table>