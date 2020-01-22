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

        .wrimagecard .fa {
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
            padding: 15px 24px;
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

    <div class="container-fluid">
        <div id="accordion">
            <div class="card mb-3">
                <div class="card-header" id="headingOne">
                    <a class="btn btn-link col-12"
                       data-toggle="collapse" data-target="#collapseOne"
                       aria-expanded="true" aria-controls="collapseOne"
                       style="text-decoration: none; text-align: left; cursor: pointer; color: #134c39">
                        <h6 class="mb-0" style="font-size: 18px"><i class="fa" aria-hidden="true"></i>
                            Enfunde</h6>
                    </a>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3 col-sm-4">
                                    <div class="wrimagecard wrimagecard-topimage">
                                        <a href="#">
                                            <div class="wrimagecard-topimage_header"
                                                 style="background-color:  rgba(19,76,57,0.1)">
                                                <center><i class="fa fa-info-circle" style="color:#134c39"> </i>
                                                </center>
                                            </div>
                                            <div class="wrimagecard-topimage_title">
                                                <h4><i class="fas fa-project-diagram"></i>
                                                    <div class="pull-right badge" id="WrInformation"
                                                         style="font-size: 18px">Enfunde
                                                    </div>
                                                </h4>
                                            </div>

                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
