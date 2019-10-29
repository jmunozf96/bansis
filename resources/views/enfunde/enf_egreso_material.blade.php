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
                    <div class="card-header text-center">
                        <h3 class="mb-0 mt-0"><i class="fas fa-clipboard-list"></i> Egreso de bodega</h3>
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

                            <div class="form-group col-md-2">
                                <label>Hacienda</label>
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
                            </div>
                            <div class="form-group col-md-3">
                                <label>Bodega</label>
                                <select class="selectpicker show-tick form-control"
                                        data-live-search="true"
                                        id="bodega" disabled>
                                    @foreach($bodegas as $bodega)
                                        <option value="{{$bodega->Id_Fila}}" {{$bodega->Id_Fila == 13 ? 'selected' : ''}}>{{$bodega->Nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-1 offset-md-1 col-6">
                                <input type="text" class="form-control {{$semana[0]->des_color}}1"
                                       id="exampleFormControlInput1"
                                       disabled>
                                <small>Presente</small>
                            </div>
                            <div class="form-group col-md-1 col-6">
                                <input type="text" class="form-control {{$semana[1]->des_color}}1"
                                       id="exampleFormControlInput1"
                                       disabled>
                                <small>Futuro</small>
                            </div>
                        </div>
                        <hr class="mt-0">
                        <div class="container-fluid p-0">
                            <div class="form-row mb-0">
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
                                <div class="form-group col-md-12">
                                    <label>Nombre | Apellido - Empleado</label>
                                    {{--<input type="text" class="form-control  form-control-lg text-dark"
                                           placeholder="Empleado"
                                           id="nombre-empleado"
                                           oninput="this.value = this.value.toUpperCase()">--}}
                                    <select class="selectpicker show-tick form-control form-control-lg"
                                            data-live-search="true" data-style="btn-outline-dark"
                                            title="Seleccionar lotero ..."
                                            id="nombre-empleado">
                                        <option value=""></option>
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

                                    </select>
                                    <small>Buscar empleado por nombre o apellido</small>
                                </div>
                            </div>
                            <div class="form-row mb-2 d-none">
                                <div class="form-group col-md-12 mb-0 d-none d-md-block d-lg-block">
                                    <input type="text" class="form-control form-control-lg bg-dark"
                                           placeholder="Detalle"
                                           value="Semana actual: {{$semana[0]->semana}} / Saldo pendiente: 0"
                                           id="detalle" style="color: #41DB00; font-size: 35px"
                                           oninput="this.value = this.value.toUpperCase()" disabled>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="presente" value="presente" name="status-semana"
                                           class="custom-control-input" checked>
                                    <label class="custom-control-label" for="presente">Despacho para cinta
                                        presente</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="futuro" value="futuro" name="status-semana"
                                           class="custom-control-input">
                                    <label class="custom-control-label" for="futuro">Despacho para cinta futuro</label>
                                </div>
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
                            <div class="form-group col-md-8  mb-0">
                                <input type="hidden" id="codigo-producto">
                                <input type="text" class="form-control  form-control-lg text-dark"
                                       placeholder="Buscar producto"
                                       id="nombre-producto" style="font-size: 20px"
                                       oninput="this.value = this.value.toUpperCase()">
                                <div class="my-1">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="id-reemplazo">
                                        <label class="custom-control-label" for="id-reemplazo">Reemplazo</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3 col-8  mb-0">
                                <input type="number" style="font-size: 20px" class="form-control bg-white form-control-lg" id="cantidad"
                                       placeholder="0.00">
                                <small>Ingrese la cantidad a despachar</small>
                            </div>
                            <div class="form-group  mb-0">
                                <button type="button" class="btn btn-primary btn-lg" style="font-size: 20px" id="add-despacho">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid col-md-12 mt-2">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="container-fluid p-0">
                            <enfunde-egreso></enfunde-egreso>
                            <div class="form-row ml-2 mr-2 mb-3">
                                <input id="detalle-total" type="text" style="font-size: 22px"
                                       class="form-control form-control-lg bg-white text-right" disabled>
                            </div>
                        </div>
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