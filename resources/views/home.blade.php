@extends('layouts.app')
@push('css')
<link href="{{ asset('css/menu_admin.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div id="accordion">
            <div class="card mb-3">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <a class="btn btn-link" data-toggle="collapse" data-target="#collapseOne"
                           aria-expanded="true" aria-controls="collapseOne"><i class="fa" aria-hidden="true"></i>
                            Collapsible Group Item #1
                        </a>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
        <div id="accordion1">
            <div class="card mb-3">
                <div class="card-header" id="headingOne1">
                    <h5 class="mb-0">
                        <a class="btn btn-link" data-toggle="collapse" data-target="#collapseOne1"
                           aria-expanded="true" aria-controls="collapseOne1"><i class="fa" aria-hidden="true"></i>
                            Collapsible Group Item #1
                        </a>
                    </h5>
                </div>
                <div id="collapseOne1" class="collapse show" aria-labelledby="headingOne1" data-parent="#accordion1">
                    <div class="card-body">

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
