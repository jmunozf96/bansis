@extends('layouts.app')
@push('css')
    <link href="{{ asset('css/menu_admin.css') }}" rel="stylesheet">
    <style>
        .wrimagecard {
            margin-top: 0;
            margin-bottom: 0.5rem;
            text-align: left;
            position: relative;
            background: #fff;
            box-shadow: 0px 0px 10px 0px rgba(46, 61, 73, 0.15);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .wrimagecard .fas {
            position: relative;
            font-size: 80px;
        }

        .wrimagecard-topimage_header {
            padding: 15px;
        }

        a.wrimagecard:hover, .wrimagecard-topimage:hover {
            box-shadow: 2px 4px 8px 0px rgba(46, 61, 73, 0.2);
        }

        .wrimagecard-topimage a {
            width: 100%;
            height: 100%;
            display: block;
        }

        .wrimagecard-topimage_title {
            display: block;
            padding: 15px 10px;
            height: 60px;
            position: relative;
        }

        .wrimagecard-topimage a {
            border-bottom: none;
            text-decoration: none;
            color: #525c65;
            transition: color 0.3s ease;
        }

    </style>
@endpush

@section('content')
    @php
        $padre_id = array(1,2)
    @endphp
    @foreach($padre_id as $recursos_padre)
        @foreach($recursos as $recurso)
            @if(intval($recurso->PadreID) == intval($recursos_padre))
                <div class="container-fluid">
                    <div id="accordion">
                        <div class="card mb-3">
                            <div class="card-header" id="headingOne">
                                <a class="btn btn-link col-12"
                                   data-toggle="collapse" data-target="#collapseOne"
                                   aria-expanded="true" aria-controls="collapseOne"
                                   style="text-decoration: none; text-align: left; cursor: pointer; color: #134c39">
                                    <h6 class="mb-0" style="font-size: 16px">
                                        <i class="fa" aria-hidden="true"></i> {{trim($recurso->Nombre)}}
                                    </h6>
                                </a>
                            </div>
                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                 data-parent="#accordion">
                                <div class="card-body">
                                    <div class="container-fluid">
                                        <div class="row">
                                            @foreach($recursos as $recurso_hijo)
                                                @if(intval($recurso_hijo->PadreID) == intval($recurso->ID))
                                                    @foreach($recursos as $recurso_hijo2)
                                                        @if(intval($recurso_hijo2->PadreID) == intval($recurso_hijo->ID))
                                                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mt-2">
                                                                <div class="wrimagecard wrimagecard-topimage">
                                                                    <a href="{{url('/sistema/'.strtolower($recurso_hijo2->modulo).'/'.strtolower($recurso_hijo2->objeto).'/'.$recurso_hijo2->ID)}}">
                                                                        <div class="wrimagecard-topimage_header"
                                                                             style="background-color:  rgba(19,76,57,0.1)">
                                                                            <center><i class="{{$recurso_hijo2->icono}}"
                                                                                       style="color:#134c39"> </i>
                                                                            </center>
                                                                        </div>
                                                                        <div class="wrimagecard-topimage_title">
                                                                            <h4>
                                                                                <div class="pull-right badge"
                                                                                     id="WrInformation"
                                                                                     style="font-size: 14px">{{explode('-',$recurso_hijo2->Nombre)[1]}}
                                                                                </div>
                                                                            </h4>
                                                                        </div>

                                                                    </a>
                                                                </div>
                                                            </div>

                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach
    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function () {
                $('.card .icon').hover(function () {
                    $(this).css('cursor', 'pointer');
                    $(this).css('background', '#ededed');
                }, function () {
                    // change to any color that was previously used.
                    $(this).css('background-color', '#ffffff');
                    $(this).css('color', 'black');
                });
            });
        </script>
    @endpush
@endsection
