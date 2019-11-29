@extends('layouts.app')
@section('title', 'Reporte Enfunde')


@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 mt-0"><i class="fas fa-file"></i> Enfunde Semana - Lotero</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{--route('enfunde.rep_semanal_data')--}}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="id-hacienda">Hacienda</label>
                                    <select class="selectpicker show-tick form-control"
                                            data-live-search="true"
                                            id="id-hacienda"
                                            name="hacienda" {{Auth::user()->idHacienda == 1 || Auth::user()->idHacienda == 2 ? 'disabled' : ''}}>
                                        <option data-tokens="343"
                                                value="150343" {{Auth::user()->idHacienda == 1 ? 'selected' : ''}}>
                                            PRIMO-BANANO
                                        </option>
                                        <option data-divider="true"></option>
                                        <option data-tokens="344"
                                                value="150344" {{Auth::user()->idHacienda == 2 ? 'selected' : ''}}>
                                            SOFCA-BANANO
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2 d-none">
                                    <label>Semana Enfunde:</label>
                                    {{Form::select('semana',$combosemanas ,'',[
                                    'class' => 'selectpicker show-tick form-control',
                                     'data-live-search' => 1,
                                     'data-size'=>"10",
                                     'title'=>"Seleccione la semana"
                                    ])}}
                                </div>
                            </div>
                            <div class="form-group d-none">
                                {{Form::button('<i class="fas fa-search"></i> Consultar', array('type' => 'button', 'class' => 'btn btn-primary'))}}
                            </div>
                        </form>
                        <hr>
                        <div class="form-row">
                            <div class="col-12 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr class="text-center" style="font-size: 16px">
                                        <th scope="col">Hacienda</th>
                                        <th scope="col">Periodo</th>
                                        <th scope="col">Semana</th>
                                        <th scope="col">Cinta_Pre</th>
                                        <th scope="col">Cinta_Fut</th>
                                        <th scope="col">Total_Pre</th>
                                        <th scope="col">Total_Fut</th>
                                        <th scope="col">Enfunde Semana</th>
                                        <th scope="col">Reporte</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($enfunde_semanal))
                                        @inject('utilidades', 'App\Http\Controllers\Sistema\UtilidadesController')
                                        @foreach($enfunde_semanal as $enfunde)
                                            <tr class="text-center table-sm" style="font-size: 16px">
                                                <td style="width: 10%">
                                                    <span class="badge badge-pill badge-success">{{$enfunde->idhacienda == 1 ? 'PRIMO' : 'SOFCA'}}</span>
                                                </td>
                                                <td style="width: 10%">
                                                    <span class="badge badge-pill badge-primary">{{$enfunde->periodo}}</span>
                                                </td>
                                                <td style="width: 10%">{{$enfunde->semana}}</td>
                                                <td style="width: 15%">
                                                    <input class="form-control {{$utilidades::getColorCodigo($enfunde->cinta_pre)->color}}1"
                                                           disabled>
                                                </td>
                                                <td style="width: 15%">
                                                    <input class="form-control {{$utilidades::getColorCodigo($enfunde->cinta_fut)->color}}1"
                                                           disabled>
                                                </td>
                                                <td style="width: 15%">{{number_format($enfunde->presente)}}</td>
                                                <td style="width: 15%">{{number_format($enfunde->futuro)}}</td>
                                                <td style="width: 15%">{{number_format($enfunde->enfunde)}}</td>
                                                <td style="width: 5%">
                                                    <div class="btn-toolbar justify-content-center" role="toolbar">
                                                        <div class="btn-group mr-1" role="group"
                                                             aria-label="Third group">
                                                            {{Form::open(['method' => 'POST', 'id' => 'pdf-view-'.$enfunde->semana,
                                                                'onsubmit' => "$('#pdf-view-".$enfunde->semana."').attr('target', '_blank')",
                                                                'route' => ['enfunde.rep_semanal_pdf']])}}
                                                            {{Form::hidden('json', json_encode(['hacienda' => $enfunde->idhacienda,
                                                            'semana' => $enfunde->semana,
                                                            'color_pre' => $utilidades::getColorCodigo($enfunde->cinta_pre)->color,
                                                            'color_fut' => $utilidades::getColorCodigo($enfunde->cinta_fut)->color
                                                            ]))}}
                                                            {{Form::button('<i class="fas fa-file-pdf"></i>', array('type' => 'submit', 'class' => 'btn btn-danger'))}}
                                                            {{Form::close()}}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <th colspan="7">No hay datos que mostrar...</th>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                <hr>
                                <div class="form-row mt-3 justify-content-center">
                                    {{ $enfunde_semanal->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection