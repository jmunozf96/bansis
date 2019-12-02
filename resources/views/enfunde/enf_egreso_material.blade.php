@extends('layouts.app')
@section('title', 'Egreso Bodega')
@section('content')

    <div class="container-fluid">
        <!--nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Library</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data</li>
            </ol>
        </nav-->
        <div class="row justify-content-center">
            <div class="container-fluid col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 mt-0"><i class="fas fa-clipboard-list"></i> Egreso de bodega</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row mt-0 mb-0">
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
                            <div class="form-group col-md-2">
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

                            <div class="form-group col-md-2 d-none">
                                <label>Codigo Empleado</label>
                                <div class="input-group flex-nowrap">
                                    <div class="input-group-prepend">
                                                    <span class="input-group-text" id="addon-wrapping">
                                                    <i class="fas fa-barcode"></i>
                                                    </span>
                                    </div>
                                    <input type="text" id="codigo-empleado"
                                           class="form-control form-control-lg bg-white" placeholder="Codigo"
                                           aria-label="Codigo" aria-describedby="addon-wrapping" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-6 mb-0">
                                {{--<input type="text" class="form-control  form-control-lg text-dark"
                                       placeholder="Empleado"
                                       id="nombre-empleado"
                                       oninput="this.value = this.value.toUpperCase()">--}}
                                <label>Lotero</label>
                                <select class="selectpicker show-tick form-control form-control-lg"
                                        data-live-search="true" data-style="btn-outline-dark" data-size="15"
                                        id="nombre-empleado">
                                    @include('enfunde.select_lotero')
                                </select>
                                <div class="my-1 mb-0">
                                    <div class="custom-control custom-checkbox mr-sm-2 mb-0 b-custom-control-lg">
                                        <input type="checkbox" class="custom-control-input" id="id-reemplazo">
                                        <label class="custom-control-label" for="id-reemplazo">Despacho
                                            reemplazo</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-3 d-none">
                                <label>Bodega</label>
                                <select class="selectpicker show-tick form-control"
                                        data-live-search="true"
                                        id="bodega" disabled>
                                    @foreach($bodegas as $bodega)
                                        <option
                                                value="{{$bodega->Id_Fila}}" {{$bodega->Id_Fila == 13 ? 'selected' : ''}}>{{$bodega->Nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="container-fluid col-md-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-8 mb-0">
                                <input type="hidden" id="codigo-producto">
                                <select class="selectpicker show-tick form-control form-control-lg"
                                        data-live-search="false" data-style="btn-outline-dark" style="font-size: 50px"
                                        id="producto">
                                    @foreach($materiales as $material)
                                        <option style="font-size: 18px"
                                                value="{{$material->codigo}}"
                                                data-subtext="Stock: {{number_format(round($material->stock))}}"
                                                {{$material->codigo == 2446 ? 'selected' : ''}}>{{$material->nombre}}</option>
                                    @endforeach
                                </select>
                                <input type="text" class="form-control form-control-lg text-dark d-none"
                                       placeholder="Buscar producto"
                                       id="nombre-producto" style="font-size: 20px"
                                       oninput="this.value = this.value.toUpperCase()">
                                <div class="custom-control custom-radio custom-control-inline mt-1 mb-0  b-custom-control-lg">
                                    <input type="radio" id="presente" value="presente" name="status-semana"
                                           class="custom-control-input" checked>
                                    <label class="custom-control-label" for="presente">Presente</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline mt-1 mb-0 b-custom-control-lg">
                                    <input type="radio" id="futuro" value="futuro" name="status-semana"
                                           class="custom-control-input">
                                    <label class="custom-control-label" for="futuro">Futuro</label>
                                </div>
                            </div>
                            <div class="form-group col-md-2 col-8  mb-0">
                                <div class="input-group mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-sort-numeric-up"></i></div>
                                    </div>
                                    <input type="number" style="font-size: 20px"
                                           class="form-control bg-white form-control-lg" id="cantidad"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div class="form-group col-md-1 mb-0">
                                <button type="button" class="btn btn-primary btn-lg" style="font-size: 20px"
                                        id="add-despacho">+
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid col-md-12 mt-2">
                <div class="card">
                    <div class="card-body p-0">
                        <enfunde-egreso></enfunde-egreso>
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
                                   href="{{route('url', [
                                'modulo' => Auth::user()->modulo,
                                'objeto' => Auth::user()->objeto,
                                'idRecurso' => Auth::user()->recursoId])}}">
                                    <i class="fas fa-sign-out-alt"></i> Salir
                                </a>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-1 col-6 mb-0 mt-1">
                                <input type="text" class="form-control {{$semana[0]->des_color}}1"
                                       id="exampleFormControlInput1"
                                       disabled>
                            </div>
                            <div class="form-group col-md-1 col-6 mb-0 mt-1">
                                <input type="text" class="form-control {{$semana[1]->des_color}}1"
                                       id="exampleFormControlInput1"
                                       disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="emp-reemplazo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Buscar lotero reemplazo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Nombre | Apellido - Empleado</label>
                                <input type="hidden" id="id-empleado-reemplazo" value="0"/>
                                <input type="text" class="form-control  form-control-lg text-dark"
                                       placeholder="Empleado"
                                       id="nombre-empleado-reemplazo"
                                       oninput="this.value = this.value.toUpperCase()">
                                <small>Buscar empleado por nombre o apellido</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-reemplazo">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
