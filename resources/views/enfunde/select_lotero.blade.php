<option value="" selected disabled>Seleccione un lotero...</option>
<optgroup label="Loteros con saldo de fundas" data-max-options="2">
    @foreach($loteros as $lotero)
        @if($lotero->fundas)
            <option data-subtext="Tiene fundas despachada"
                    value="{{$lotero->idempleado}}" {{isset($_GET['lotero']) ? $lotero->idempleado == $_GET['lotero'] ? 'selected' : '' : ''}}>{{$lotero->empleado->nombre}}</option>
        @endif
    @endforeach
</optgroup>
<optgroup label="Loteros pendientes despacho" data-max-options="2">
    @foreach($loteros as $lotero)
        @if(!$lotero->fundas)
            <option value="{{$lotero->idempleado}}">{{$lotero->empleado->nombre}}</option>
        @endif
    @endforeach
</optgroup>