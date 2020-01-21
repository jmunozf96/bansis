@extends('layouts.app')
@section('title', 'Registro Enfunde')

@section('content')
    <div class="container-fluid">
        {{--<nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Library</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data</li>
            </ol>
        </nav>--}}
        <div class="row justify-content-center">
            <div class="container-fluid col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 mt-0"><i class="fas fa-clipboard-list"></i> Registro de enfunde</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row mt-0">
                            <div class="form-group col-md-2 col-12">
                                <label>Fecha</label>
                                <input type="text" class="form-control bg-white" name="fecha" id="fecha"
                                       value="{{date("d/m/Y", strtotime(date("d-m-Y")))}}" readonly>
                            </div>
                            <div class="form-group col-md-1 col-6">
                                <label>Semana</label>
                                <input type="text" class="form-control bg-white" id="semana"
                                       value="{{$semana[0]->semana}}" disabled>
                            </div>
                            <div class="form-group col-md-1 col-6">
                                <label>Periodo</label>
                                <input type="text" class="form-control bg-white" id="periodo"
                                       value="{{$semana[0]->periodo}}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label>Hacienda</label>
                                <select class="selectpicker show-tick form-control"
                                        data-live-search="true"
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
                            </div>
                            <div class="form-group col-md-2 offset-md-1 col-6">
                                <input type="text" class="form-control {{$semana[0]->des_color}}1"
                                       id="color_presente"
                                       disabled>
                                <small>Presente</small>
                            </div>
                            <div class="form-group col-md-2 col-6">
                                <input type="text" class="form-control {{$semana[1]->des_color}}1"
                                       id="color_futuro"
                                       disabled>
                                <small>Futuro</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="container-fluid col-md-12 mt-2">
                <div class="card">
                    <div class="card-body p-1">
                        <enfunde-registro :loteros="{{ $loteros }}"></enfunde-registro>
                    </div>
                    <div class="card-footer">
                        <div class="form-row float-right">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-primary btn-lg" id="btn-nuevo">
                                    <i class="fa fa-file"></i> Nuevo
                                </button>
                                <button type="button" class="btn btn-success btn-lg" id="btn-save">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <a class="btn btn-danger btn-lg"
                                   href="{{route('enfunde',['objeto' =>  $objeto, 'modulo' => $modulo])}}">
                                    <i class="fas fa-sign-out-alt"></i> Salir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
