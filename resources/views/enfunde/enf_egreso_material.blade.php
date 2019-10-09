@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-left mb-0"><h3 class="mb-0"><i class="fas fa-dolly"></i> Egreso de
                            bodega</h3></div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="form-row mt-0">
                                <div class="form-group col-2">
                                    <input type="text" class="form-control bg-white" id="exampleFormControlInput1"
                                           placeholder="Semana 35" readonly>
                                </div>
                                <div class="form-group col-2">
                                    <input type="text" class="form-control bg-white" id="exampleFormControlInput1"
                                           placeholder="Semana 35" readonly>
                                </div>
                                <div class="form-group col-2">
                                    <input type="text" class="form-control bg-white" id="exampleFormControlInput1"
                                           placeholder="Periodo 10" readonly>
                                </div>
                                <div class="form-group col-2 offset-2">
                                    <input type="text" class="form-control bg-white" id="exampleFormControlInput1"
                                           placeholder="Presente" readonly>
                                </div>
                                <div class="form-group col-2">
                                    <input type="text" class="form-control bg-white" id="exampleFormControlInput1"
                                           placeholder="Futuro" readonly>
                                </div>
                            </div>
                            <hr class="mt-0">
                            <div class="card">
                                <div class="card-body ">
                                    <div class="container-fluid p-0">
                                        <div class="form-row">
                                            <div class="form-group col-2">
                                                <label>Codigo Empleado</label>
                                                <div class="input-group flex-nowrap">
                                                    <div class="input-group-prepend">
                                                    <span class="input-group-text" id="addon-wrapping">
                                                    <i class="fas fa-barcode"></i>
                                                    </span>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="addon-wrapping">
                                                </div>
                                            </div>
                                            <div class="form-group col-7">
                                                <label>Nombre | Apellido - Empleado</label>
                                                <input type="text" class="form-control" placeholder="Empleado">
                                                <small>Buscar empleado por nombre o apellido</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection