@extends('layouts.app')
@section('title', 'Egreso Bodega')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="form-row mb-0 ml-1">
                    <div class="form-row col-8">
                        <div class="form-group col-md-2 col-6 mb-0">
                            <input type="text" class="form-control bg-white" id="semana"
                                   value="{{$semana[0]->semana}}" disabled>
                            <small class="ml-1">Semana</small>
                        </div>
                        <div class="form-group col-md-2 col-6 mb-0">
                            <input type="text" class="form-control bg-white" id="periodo"
                                   value="{{$semana[0]->periodo}}" disabled>
                            <small class="ml-1">Periodo</small>
                        </div>
                        <div class="form-group col-md-8 mb-0">
                            <select class="selectpicker show-tick form-control"
                                    data-live-search="true"
                                    id="id-hacienda" {{Auth::user()->id_hacienda == 1 || Auth::user()->id_hacienda == 2 ? 'disabled' : ''}}>
                                <option data-tokens="343"
                                        value="343" {{Auth::user()->id_hacienda == 1 ? 'selected' : ''}}>
                                    PRIMO-BANANO
                                </option>
                                <option data-divider="true"></option>
                                <option data-tokens="344"
                                        value="344" {{Auth::user()->id_hacienda == 2 ? 'selected' : ''}}>
                                    SOFCA-BANANO
                                </option>
                            </select>
                            <small class="ml-1">Seleccionar hacienda</small>
                        </div>
                    </div>
                    <div class="form-row col-4 justify-content-end">
                        <div class="form-group">
                            <a class="btn btn-success btn-lg" href="{{route('despacho',
                            ['objeto' =>  Auth::user()->objeto,
                            'idRecurso' => Auth::user()->recursoId]
                            )}}"> <i class="fas fa-plus"></i> Nuevo despacho </a>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="container-fluid">
                    <div class="form-row justify-content-center">
                        @foreach($egresos as $egreso)
                            <div class="">
                                <div class="card m-1" style="width: 18rem;">
                                    <div class="card-header text-center mb-0">
                                        <h5 class="card-title mb-0" style="font-size: 16px">
                                            {{$egreso->empleado->nombre}}
                                        </h5>
                                    </div>
                                    <div class="card-body text-center">
                                    <!--img class="card-img-top" src="{{URL::asset('/img/lotero.png')}}" style="width: 40%"-->
                                        <p class="card-text mb-0">Despacho total:
                                            <b>{{intval($egreso->total)}}</b>
                                            fundas.</p>
                                        <p class="card-text mt-0 mb-3">
                                            @if(!$egreso->status)
                                                <span class="badge badge-pill badge-success">ENFUNDE CERRADO</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">ENFUNDE PENDIENTE</span>
                                            @endif
                                        </p>
                                        <a href="#" class="btn btn-primary"><i class="fas fa-eye"></i> Mostrar detalles</a>

                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach($egreso->egresos as $despacho)
                                            <li class="list-group-item text-left"><b>{{$despacho->fecha}}:</b> Salen
                                                <b>{{intval($despacho->cantidad)}}</b> fundas.
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <hr>
                    <div class="form-row mt-3 justify-content-center">
                        {{ $egresos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection