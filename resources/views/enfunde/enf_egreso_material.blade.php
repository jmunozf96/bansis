@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Library</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data</li>
            </ol>
        </nav>
        <div class="row justify-content-center">
            <div class="container-fluid col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row mt-0">
                            <div class="form-group col-md-2 col-12">
                                <label>Fecha</label>
                                <input type="text" class="form-control bg-white" name="fecha" id="fecha"
                                       value="{{date("d/m/Y", strtotime(date("d-m-Y")))}}" readonly>
                            </div>
                            <div class="form-group col-md-1 col-6">
                                <label>Semana</label>
                                <input type="text" class="form-control bg-white" id="exampleFormControlInput1"
                                       value="{{$semana[0]->semana}}" disabled>
                            </div>
                            <div class="form-group col-md-1 col-6">
                                <label>Periodo</label>
                                <input type="text" class="form-control bg-white" id="exampleFormControlInput1"
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
                                        id="id-hacienda">
                                    @foreach($bodegas as $bodega)
                                        <option>{{$bodega->Nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-1 offset-md-1 col-6">
                                <input type="text" class="form-control {{$semana[0]->des_color}}1" id="exampleFormControlInput1"
                                       disabled>
                                <small>Presente</small>
                            </div>
                            <div class="form-group col-md-1 col-6">
                                <input type="text" class="form-control {{$semana[1]->des_color}}1" id="exampleFormControlInput1"
                                       disabled>
                                <small>Futuro</small>
                            </div>
                        </div>
                        <hr class="mt-0">
                        <div class="container-fluid p-0">
                            <div class="form-row">
                                <div class="form-group col-md-2">
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
                                <div class="form-group col-md-10">
                                    <label>Nombre | Apellido - Empleado</label>
                                    <input type="text" class="form-control  form-control-lg text-dark"
                                           placeholder="Empleado"
                                           id="nombre-empleado"
                                           oninput="this.value = this.value.toUpperCase()">
                                    <small>Buscar empleado por nombre o apellido</small>
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
                            <div class="form-group col-md-12">
                                <input type="hidden" id="codigo-producto">
                                <input type="text" class="form-control  form-control-lg text-dark"
                                       placeholder="Buscar producto"
                                       id="nombre-producto"
                                       oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>
                        <div class="form-row mt-0">
                            <div class="form-group col-md-8 mb-0 d-none d-md-block d-lg-block">
                                <input type="text" class="form-control form-control-lg bg-dark"
                                       placeholder="Empleado" value="0.00"
                                       id="nombre-empleado" style="color: #41DB00; font-size: 35px"
                                       oninput="this.value = this.value.toUpperCase()" disabled>
                            </div>
                            <div class="form-group col-md-3 col-8">
                                <input type="number" class="form-control bg-white form-control-lg" id="exampleFormControlInput1"
                                       placeholder="0.00">
                                <small>Ingrese la cantidad a despachar</small>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary btn-lg">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid col-md-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <enfunde-registro></enfunde-registro>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection