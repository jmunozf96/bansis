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
                            <div class="form-group col-md-2">
                                {{Form::select('hacienda',Auth::user()->idHacienda == 0 || Auth::user()->idHacienda == 1  ? ['1' => 'PRIMOBANANO'] : ['2' => 'SOFCABANANO'],null,
                                ['class' => 'selectpicker show-tick form-control',
                                 'data-live-search' => 0,
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
                            <div class="form-group col-md-6">
                                {{Form::select('lotero',$comboloteros ,'',[
                                'class' => 'selectpicker show-tick form-control',
                                 'data-live-search' => 1,
                                 'data-size'=>"10",
                                 'title'=>"Seleccione un lotero",
                                 'multiple'=>'multiple',
                                 'name' => 'lotero[]',
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
                                    <tr class="text-center" style="font-size: 16px">
                                        <th scope="col">Periodo</th>
                                        <th scope="col">Semana</th>
                                        <th scope="col">Cinta_Pre</th>
                                        <th scope="col">Cinta_Fut</th>
                                        <th scope="col">Lotero</th>
                                        <th scope="col">Total_Pre</th>
                                        <th scope="col">Total_Fut</th>
                                        <th scope="col">Semana</th>
                                        <th scope="col">Desbunche</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(\Session::has('data_enfunde') && count(\Session::get('data_enfunde')) >0)
                                        @inject('utilidades', 'App\Http\Controllers\Sistema\UtilidadesController')
                                        <?php $total_pre = 0 ?>
                                        <?php $total_fut = 0 ?>
                                        <?php $total_desb = 0 ?>
                                        @foreach(Session::get('data_enfunde') as $enfunde)
                                            <tr class="text-center table-sm" style="font-size: 16px">
                                                <td>
                                                    <span class="badge badge-pill badge-primary">{{$enfunde->periodo}}</span>
                                                </td>
                                                <td>{{$enfunde->semana}}</td>
                                                <td style="width: 6%">
                                                    <input class="form-control {{$utilidades::getSemana($enfunde->fecha)[0]->des_color}}1"
                                                           disabled>
                                                </td>
                                                <td style="width: 6%">
                                                    <input class="form-control {{$utilidades::getSemana($enfunde->fecha)[1]->des_color}}1"
                                                           disabled>
                                                </td>
                                                <td><b>{{$enfunde->lotero->nombres}}</b></td>
                                                <td>{{$enfunde->total_pre}}</td>
                                                <td>{{$enfunde->total_fut}}</td>
                                                <td>{{+$enfunde->total_pre + +$enfunde->total_fut}}</td>
                                                <td>
                                                    <span class="badge badge-pill badge-danger">{{$enfunde->chapeo}}</span>
                                                </td>
                                            </tr>
                                            <?php $total_pre += +$enfunde->total_pre?>
                                            <?php $total_fut += +$enfunde->total_fut?>
                                            <?php $total_desb += +$enfunde->chapeo?>
                                        @endforeach
                                        <tr class="bg-light">
                                            <td colspan="5"><h2><b>TOTAL ENFUNDE</b></h2></td>
                                            <td class="text-center"><h3><b>{{number_format($total_pre)}}</b></h3></td>
                                            <td class="text-center"><h3><b>{{number_format($total_fut)}}</b></h3></td>
                                            <td class="text-center"><h3>
                                                    <b>{{number_format(+$total_pre + +$total_fut)}}</b></h3></td>
                                            <td class="text-center"><h3><b>{{number_format($total_desb)}}</b></h3></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th colspan="7">No hay datos que mostrar...</th>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection