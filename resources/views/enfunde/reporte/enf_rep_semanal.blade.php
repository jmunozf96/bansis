@extends('layouts.app')
@section('title', 'Reporte Enfunde')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 mt-0"><i class="fas fa-file-alt"></i> Reporte Semanal</h5>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => ['enfunde.reporte.semanal'], 'method' => 'POST']) !!}
                        {{Form::token()}}
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                {{Form::select('hacienda',['343' => 'PRIMOBANANO', ['data-divider' => true] ,'344' => 'SOFCABANANO'],'',
                                ['class' => 'selectpicker show-tick form-control',
                                 'data-live-search' => 0
                                ])}}
                            </div>
                            <div class="form-group col-md-2">
                                {{Form::select('semana',$combosemanas ,'',[
                                'class' => 'selectpicker show-tick form-control',
                                 'data-live-search' => 1,
                                 'data-size'=>"10",
                                 'title'=>"Seleccione la semana"
                                ])}}
                            </div>
                            <div class="form-group col-md-4">
                                {{Form::select('lotero',$comboloteros ,'',[
                                'class' => 'selectpicker show-tick form-control',
                                 'data-live-search' => 1,
                                 'data-size'=>"10",
                                 'title'=>"Seleccione un lotero"
                                ])}}
                            </div>
                            <div class="form-group">
                                {{Form::button('<i class="fas fa-search"></i> Consultar', array('type' => 'submit', 'class' => 'btn btn-primary'))}}
                            </div>
                        </div>
                        {!! Form::close() !!}
                        <div class="form-row">
                            <div class="col-12 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th scope="col">Periodo</th>
                                        <th scope="col">Semana</th>
                                        <th scope="col">Cinta_Pre</th>
                                        <th scope="col">Cinta_Fut</th>
                                        <th scope="col">Lotero</th>
                                        <th scope="col">Total_Pre</th>
                                        <th scope="col">Total_Fut</th>
                                        <th scope="col">Desbunche</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(\Session::has('data_enfunde') && count(\Session::get('data_enfunde')) >0)
                                        @foreach(Session::get('data_enfunde') as $enfunde)
                                            <tr class="text-center">
                                                <td>{{$enfunde->periodo}}</td>
                                                <td>{{$enfunde->semana}}</td>
                                                <td>
                                                    {{ route('calendario', $enfunde->fecha) }}
                                                    <input class="form-control {{\App\Http\Controllers\Sistema\UtilidadesController::getSemana($enfunde->fecha)[0]->des_color}}1"
                                                           disabled>
                                                </td>
                                                <td>
                                                    <input class="form-control {{\App\Http\Controllers\Sistema\UtilidadesController::getSemana($enfunde->fecha)[1]->des_color}}1"
                                                           disabled>
                                                </td>
                                                <td>{{$enfunde->lotero->nombres}}</td>
                                                <td>{{$enfunde->total_pre}}</td>
                                                <td>{{$enfunde->total_fut}}</td>
                                                <td>{{$enfunde->chapeo}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <th colspan="7">No hay datos que mostrar...</th>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                @if(Session::has('data_enfunde'))
                                    {{ Session::get('data_enfunde')->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection