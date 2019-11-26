<option value="" selected disabled>Seleccione un lotero...</option>
<optgroup label="Loteros con saldo de fundas" data-max-options="2">
    @foreach($loteros as $lotero)
        @if($lotero->fundas)
            <option data-subtext="Tiene fundas despachada" style="font-size: 18px"
                    value="{{$lotero->idempleado}}" {{isset($_GET['lotero']) ? $lotero->idempleado == $_GET['lotero'] ? 'selected' : '' : ''}}>{{$lotero->nombres}}</option>
        @endif
    @endforeach
</optgroup>
<optgroup label="Loteros pendientes despacho" data-max-options="2">
    @foreach($loteros as $lotero)
        @if(!$lotero->fundas)
            <option style="font-size: 18px" value="{{$lotero->idempleado}}">{{$lotero->nombres}}</option>
        @endif
    @endforeach
</optgroup>
