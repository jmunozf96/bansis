@extends('layouts.app')
@section('title', 'Egreso Bodega')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box-open"></i> Egresos de bodega</h5>
            </div>
            <div class="card-body">
                <div class="form-row mb-0 ml-1 d-none">
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
                                    data-live-search="true" data-style="btn-outline-dark"
                                    id="id-hacienda" {{Auth::user()->idHacienda == 1 || Auth::user()->idHacienda == 2 ? 'disabled' : ''}}>
                                <option data-tokens="343"
                                        value="343" {{Auth::user()->idHacienda == 1 ? 'selected' : ''}}>
                                    PRIMO-BANANO
                                </option>
                                <option data-divider="true"></option>
                                <option data-tokens="344"
                                        value="344" {{Auth::user()->idHacienda == 2 ? 'selected' : ''}}>
                                    SOFCA-BANANO
                                </option>
                            </select>
                            <small class="ml-1">Seleccionar hacienda</small>
                        </div>
                    </div>
                    <div class="form-row col-4 justify-content-end">
                        <div class="form-group">

                        </div>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col p-0">
                            <table class="table table-bordered">
                                <th class="text-center" style="vertical-align: inherit; font-size: 20px">
                                    Accion</th>
                                <th class="text-center" style="vertical-align: inherit; font-size: 20px">
                                    Enfunde</th>
                                <th class="text-center" colspan="2" style="vertical-align: inherit; font-size: 20px">
                                    Lotero</th>
                                <th>
                                    <a class="btn btn-dark btn-lg btn-block" href="{{route('despacho',
                                    ['modulo' => Auth::user()->modulo,
                                    'objeto' =>  Auth::user()->objeto,
                                    'idRecurso' => Auth::user()->recursoId,]
                                    )}}"> <i class="fas fa-plus"></i> Nuevo despacho </a>
                                </th>
                                @foreach($egresos as $egreso)
                                    <tr>
                                        <td class="text-center w-auto mt-auto mb-auto" style="vertical-align: inherit;">
                                            <a href="{{route('despacho',
                                                ['modulo' => Auth::user()->modulo,
                                                'objeto' =>  Auth::user()->objeto,
                                                'idRecurso' => Auth::user()->recursoId,
                                                'lotero' => $egreso->idempleado]
                                                )}}" class="btn btn-success"><i class="fas fa-eye"></i></a>
                                        </td>
                                        <td class="text-center w-auto mt-auto mb-auto" style="vertical-align: inherit; font-size: 18px">
                                            @if(!$egreso->status)
                                                <span class="badge badge-pill badge-success">C</span>
                                            @else
                                                <span class="badge badge-pill badge-warning">A</span>
                                            @endif
                                        </td>
                                        <td class="text-center" style="vertical-align: inherit; width: 5%">
                                            <img class="" src="{{URL::asset('/img/user.png')}}" style="width: 50%">
                                        </td>
                                        <td class="text-center w-auto mt-auto mb-auto" style="vertical-align: inherit">
                                            {{$egreso->empleado->nombre}}
                                        </td>
                                        <td class="p-2 mb-0">
                                            <table class="table table-bordered mb-0 table-sm">
                                                <th colspan="3" class="table-active">Despacho total: <b>{{intval($egreso->total)}}</b>
                                                    fundas.
                                                </th>
                                                @foreach($egreso->egresos as $despacho)
                                                    <tr>
                                                        <td style="vertical-align: inherit; width: 80%">{{$despacho->get_material->nombre}}</td>
                                                        <td class="text-center" style="vertical-align: inherit; width: 10%">{{intval($despacho->cantidad)}}</td>
                                                        <td class="text-center w-auto"
                                                            style="vertical-align: inherit; color: red; width: 10%">
                                                            <b>{{$despacho->presente ? 'P' : 'F'}}</b></td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            <p class="mt-0">
                                <b>Status</b> <span class="badge badge-pill badge-warning">A</span> para enfunde abierto | <span class="badge badge-pill badge-success">C</span> para enfunde cerrado.
                            </p>
                        </div>
                        <hr>
                    </div>
                    <div class="form-row mt-3 justify-content-center">
                        {{ $egresos->links() }}
                    </div>
                </div>
                {{--<div class="container-fluid">
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
                                        <a href="{{route('despacho',
                                                ['modulo' => Auth::user()->modulo,
                                                'objeto' =>  Auth::user()->objeto,
                                                'idRecurso' => Auth::user()->recursoId,
                                                'lotero' => $egreso->idempleado]
                                                )}}" class="btn btn-primary"><i class="fas fa-eye"></i> Mostrar detalles</a>

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
                </div>--}}
            </div>
        </div>
    </div>
@endsection