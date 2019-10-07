@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header p-2" id="titulo">

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-0">
                        <div class="row" id="id-buscador">
                            <div class="col-4">
                                <div class="form-group mb-0">
                                    <select class="selectpicker show-tick form-control"
                                            data-live-search="true" id="id-hacienda">
                                        <option data-tokens="343" value="343">PRIMO-BANANO</option>
                                        <option data-divider="true"></option>
                                        <option data-tokens="344" value="344">SOFCA-BANANO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group mb-0">
                                    <input type="text" name="datetimes" class="form-control bg-white" readonly=""
                                           id="daterange"/>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group mb-0">
                                    <button type="button" class="btn btn-primary form-control" id="id-btn-refresh">
                                        Refrescar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr>
                        <div class="d-flex justify-content-center">
                            <div class="spinner-grow" role="status" id="id-loading" style="width: 6rem; height: 6rem;">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <empacadora-cajas></empacadora-cajas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="embalador-detalle-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo_embalador">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <table class="table table-bordered" id="table-embaladores-cajas">
                            <thead>
                            <tr>
                                <th class="text-center" scope="col"># Embalador</th>
                                <th class="text-center" scope="col">Caja</th>
                                <th class="text-center" scope="col">Total</th>
                            </tr>
                            </thead>
                            <tbody id="detalle-cajas" class="table-sm">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modalcuerpo')
    <div class="container-fluid">
        <table class="table table-bordered" id="table-embaladores">
            <thead>
            <tr>
                <th class="text-center" scope="col"># Embalador</th>
                <th class="text-center" scope="col">Estado</th>
                <th class="text-center" scope="col">N. Cajas</th>
                <th class="text-center" scope="col">Peso</th>
            </tr>
            </thead>
            <tbody id="detalle" class="table-sm">
            </tbody>
        </table>
    </div>
@endsection
