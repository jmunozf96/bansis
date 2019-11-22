@extends('layouts.app')
@section('title', 'Liquidacion Cajas')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Liquidacion Dole</h5>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <form method="POST" action="{{route('produccion.liquid.upload')}}" enctype="multipart/form-data"
                          novalidate>
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
                            <div class="col-md-2">
                                <label for="validationCustom03">Semana</label>
                                <input type="number" class="form-control" id="id-semana" name="semana" min="1" max="52"
                                       value="{{ old('semana') }}"
                                       placeholder="Ingresar # semana"
                                       required>
                                @if($errors->has('semana'))
                                    <small style="color: red">{{$errors->first('semana')}}</small>
                                @endif

                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02">Liquidacion </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input"
                                           accept="text/html"
                                           id="id-archivo" name="archivo">
                                    <label class="custom-file-label" for="customFile">Buscar archivos</label>
                                </div>
                                @if($errors->has('archivo'))
                                    <small style="color: red">{{$errors->first('archivo')}}</small>
                                @endif
                                @push('scripts')
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $(".custom-file-input").on("change", function () {
                                            var fileName = $(this).val().split("\\").pop();
                                            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                                        });
                                    });
                                </script>
                                @endpush
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3" type="submit">
                            <i class="fas fa-upload"></i> Cargar Información
                        </button>
                    </form>
                </div>
                @if(\Session::has('liquidacion'))
                    <?php $liquidacion = \Session::get('liquidacion') ?>
                    <div class="container-fluid">
                        <hr>
                        @if(\Session::has('message'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{\Session::get('message')}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="id-documento">Documento</label>
                                <input type="text" class="form-control bg-white" id="id-documento"
                                       value="{{$liquidacion->numero}}"
                                       readonly>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="id-codfinc">Codigo Finca</label>
                                <input type="text" class="form-control bg-white" id="id-codfinca"
                                       value="{{$liquidacion->codFinca}}"
                                       readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="id-nomfinca">Finca</label>
                                <input type="text" class="form-control bg-white" id="id-nomfinca"
                                       value="{{$liquidacion->nomFinca}}"
                                       readonly>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="id-rucfinca">Ruc Finca</label>
                                <input type="text" class="form-control bg-white" id="id-rucfinca"
                                       value="{{$liquidacion->rucFinda}}"
                                       readonly>
                            </div>
                        </div>
                        <liquidacion-registro
                                v-bind:liquidaciones="{{json_encode($liquidacion->cajas)}}"></liquidacion-registro>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


