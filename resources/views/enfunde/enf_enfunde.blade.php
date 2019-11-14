@extends('layouts.app')
@section('title', 'Enfunde')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-street-view"></i> Enfunde Semanal</h5>
            </div>
            <div class="card-body">
                <div class="form-row mb-0 ml-1">
                    <div class="form-row col-8 d-none">
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

                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col mb-0">
                        <div class="form-group">
                            {!! Form::open(['method'=>'GET','class'=>'navbar-form navbar-left','role'=>'search'])  !!}
                            <div class="input-group custom-search-form mb-0">
                                <div class="input-group-prepend">
                                    <a class="btn btn-dark btn-lg" href="{{route('enfunde',
                                    ['modulo' => Auth::user()->modulo,
                                    'objeto' =>  Auth::user()->objeto,
                                    'idRecurso' => Auth::user()->recursoId,]
                                    )}}"> <i class="fas fa-plus"></i> Nuevo
                                    </a>
                                    <a href="{{route('url',
                                    ['modulo' => Auth::user()->modulo,
                                    'objeto' =>  Auth::user()->objeto,
                                    'idRecurso' => Auth::user()->recursoId,]
                                    )}}" class="btn btn-danger btn-lg text-white"><i class="fas fa-sync"></i></a>
                                </div>
                                <input type="text" class="form-control form-control-lg mb-0" name="search"
                                       oninput="this.value = this.value.toUpperCase()"
                                       value="{{ request()->input('search', old('search')) }}"
                                       placeholder="Buscar por nombre de lotero...">
                                <span class="input-group-btn">
                            <button class="btn btn-default-sm btn-lg" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col p-0">
                        @if(\Session::has('msg'))
                            @push('scripts')
                            <script type="text/javascript">
                            </script>
                            @endpush
                            <div class="alert alert-{{\Session::get('status')}} alert-dismissible fade show"
                                 role="alert">
                                <i class="fas fa-exclamation-circle"></i> <a href="javascript:void(0)"
                                                                             class="alert-link">{!! \Session::get('msg') !!}</a>.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <table class="table table-hover table-striped" style="width:100%">
                            <thead>
                            <tr class="text-center">
                                <th scope="col">...</th>
                                <th scope="col">Ir</th>
                                <th scope="col">Hacienda</th>
                                <th scope="col">Semana</th>
                                <th scope="col">Lotero</th>
                                <th scope="col">Col_pre</th>
                                <th scope="col">Tot_pre</th>
                                <th scope="col">...</th>
                                <th scope="col">Col_fut</th>
                                <th scope="col">Tot_fut</th>
                                <th scope="col">...</th>
                                <th scope="col">Total</th>
                                <th scope="col">Accion</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($enfundes_pendientes as $enfunde)
                                <tr style="font-size: 16px" class="text-center table-sm">
                                    <th style="width: 5%"><span class="badge badge-warning">A</span></th>
                                    <th>
                                        <a class="btn btn-primary text-white">
                                            <i class="fas fa-folder-open"></i>
                                        </a>
                                    </th>
                                    <th scope="row"
                                        style="width: 10%">{{$enfunde->idhacienda == 1 ? '343' : '344'}}</th>
                                    <td style="width: 5%">{{$enfunde->semana}}</td>
                                    <td>{{$enfunde->lotero->nombre_1 . ' ' . $enfunde->lotero->apellido_1 . ' ' . $enfunde->lotero->apellido_2}}</td>
                                    <td style="width: 5%">
                                        <input
                                                class="form-control {{\App\Http\Controllers\Sistema\UtilidadesController::getSemana($enfunde->fecha)[0]->des_color}}1"
                                                disabled>
                                    </td>
                                    <td style="width: 5%">
                                        <input class="form-control text-center bg-white" type="number"
                                               value="{{$enfunde->total_pre}}" disabled>
                                    </td>
                                    <td>
                                        <div class="btn-group mr-1" role="group" aria-label="First group">
                                            {{Form::open(['method' => 'DELETE',
                                            'onsubmit' => 'return confirm("¿Deseas eliminar el registro Presente?")',
                                            'route' => ['enfunde.delete_presente',
                                            $enfunde->idlotero,$enfunde->semana]])}}
                                            {{Form::button('<i class="fas fa-times"></i>', array('type' => 'submit', 'class' => 'btn btn-danger'))}}
                                            {{Form::close()}}
                                        </div>
                                    </td>
                                    <td style="width: 5%">
                                        <input
                                                class="form-control {{\App\Http\Controllers\Sistema\UtilidadesController::getSemana($enfunde->fecha)[1]->des_color}}1"
                                                disabled>
                                    </td>
                                    <td style="width: 5%">
                                        <input class="form-control text-center bg-white" type="number"
                                               value="{{$enfunde->total_fut}}" disabled>
                                    </td>
                                    <td>
                                        <div class="btn-group mr-1" role="group" aria-label="Second group">
                                            {{Form::open(['method' => 'DELETE',
                                                'onsubmit' => 'return confirm("¿Deseas eliminar el registro Futuro?")',
                                                'route' => ['enfunde.delete_futuro',
                                                $enfunde->idlotero,$enfunde->semana]])}}
                                            {{Form::button('<i class="fas fa-times"></i>', array('type' => 'submit', 'class' => 'btn btn-danger'))}}
                                            {{Form::close()}}
                                        </div>
                                    </td>
                                    <td style="width: 5%">
                                        <input class="form-control text-center bg-white"
                                               value="{{+$enfunde->total_pre + +$enfunde->total_fut}}" disabled>
                                    </td>
                                    <td>
                                        <div class="btn-toolbar justify-content-center" role="toolbar">
                                            <div class="btn-group mr-1" role="group" aria-label="Third group">
                                                <button type="button" class="btn btn-primary">
                                                    <i class="fas fa-lock"></i> Cerrar
                                                </button>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <!--tr>
                        <td colspan="10">Larry the Bird</td>
                    </--tr-->
                            </tbody>
                        </table>
                        <hr>
                        <div class="form-row mt-3 justify-content-center">
                            {{ $enfundes_pendientes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
