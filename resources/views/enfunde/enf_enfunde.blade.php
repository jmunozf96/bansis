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
            </div>
        </div>
    </div>
@endsection