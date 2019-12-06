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
            <div class="card-body p-1">
                <div class="row" style="margin-left: 0%; margin-right: 0%; margin-bottom: -0.5%; margin-top: 0.5%">
                    @for($x = 1; $x<10;$x++)
                        <div class="form-group mb-2 col-lg-2 col-md-3 col-6 col-sm-4">
                            <div class="card icon modulo" style="margin-bottom: 5%; max-width: none">
                                <div class="card-body mb-0" style="padding-bottom: 3%">
                                    <img src="/img/alu_malla.png" alt="Paris"
                                         style="width: 40%; display: block; margin: 0 auto">
                                    <hr class="p-0 mb-0" style="box-shadow: 0 1px 1px 0 rgba(0,0,0,0.2);">
                                    <h6 class="card-title text-center mt-2 mb-0" style="display: block">
                                        ENFUNDE SEMANA LOTERO
                                    </h6>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
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