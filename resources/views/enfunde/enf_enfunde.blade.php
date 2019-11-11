@extends('layouts.app')
@section('title', 'Enfunde')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-street-view"></i> Enfunde Semanal</h3>
            </div>
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
                                    data-live-search="true" data-style="btn-outline-dark"
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
                            <a class="btn btn-success btn-lg" href="{{route('enfunde',
                            ['modulo' => Auth::user()->modulo,
                            'objeto' =>  Auth::user()->objeto,
                            'idRecurso' => Auth::user()->recursoId,]
                            )}}"> <i class="fas fa-plus"></i> Nuevo Registro Enfunde</a>
                        </div>
                    </div>
                </div>
                <hr>
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr class="text-center">
                        <th scope="col">...</th>
                        <th scope="col">Hacienda</th>
                        <th scope="col">Semana</th>
                        <th scope="col">Lotero</th>
                        <th scope="col">Col_pre</th>
                        <th scope="col">Tot_pre</th>
                        <th scope="col">Col_fut</th>
                        <th scope="col">Tot_fut</th>
                        <th scope="col">Total</th>
                        <th scope="col">Accion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($enfundes_pendientes as $enfunde)
                        <tr style="font-size: 16px" class="text-center table-sm">
                            <th style="width: 5%"><span class="badge badge-primary">P</span></th>
                            <th scope="row" style="width: 10%">{{$enfunde->idhacienda}}</th>
                            <td style="width: 5%">{{$enfunde->semana}}</td>
                            <td>{{trim($enfunde->lotero->empleado->nombre)}}</td>
                            <td style="width: 5%">
                                <input class="form-control {{\App\Http\Controllers\Sistema\UtilidadesController::getSemana($enfunde->fecha)[0]->des_color}}1"
                                       disabled>
                            </td>
                            <td><b>{{$enfunde->total_pre}}</b></td>
                            <td style="width: 5%">
                                <input class="form-control {{\App\Http\Controllers\Sistema\UtilidadesController::getSemana($enfunde->fecha)[1]->des_color}}1"
                                       disabled>
                            </td>
                            <td><b>{{$enfunde->total_fut}}</b></td>
                            <td>{{+$enfunde->total_pre + +$enfunde->total_fut}}</td>
                            <td>
                                <div class="btn-toolbar justify-content-center" role="toolbar">
                                    <div class="btn-group mr-1" role="group" aria-label="First group">
                                        <button type="button" class="btn btn-primary">
                                            <i class="fas fa-trash"></i> Presente
                                        </button>
                                        <button type="button" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Futuro
                                        </button>
                                    </div>
                                    <div class="btn-group mr-1" role="group" aria-label="Second group">
                                        <button type="button" class="btn btn-success">
                                            <i class="fas fa-lock"></i> Cerrar
                                        </button>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="10">Larry the Bird</td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-row mt-3 justify-content-center">
                    {{ $enfundes_pendientes->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection