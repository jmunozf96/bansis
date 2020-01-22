@extends('layouts.app')

@push('css')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="d-none d-lg-block d-md-block col-md-5 col-lg-7 bg-image" style="min-height: 93.8vh;" id="bg-image"></div>
            <div class="col-md-7 col-lg-5">
                <div class="login d-flex align-items-center">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-9 mt-5" style="">
                                <div class="card" style="">
                                    <h5 style="margin-left: 1rem; margin-top: 1rem; margin-bottom: -1%;"><b>{{ __('Login') }}</b></h5>
                                    <hr style="margin-bottom: -0.5em">
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label for="Nombre" class="">Usuario</label>
                                                    <input id="Nombre" type="text"
                                                           class="form-control @error('Nombre') is-invalid @enderror"
                                                           name="Nombre" value="{{ old('Nombre') }}" required
                                                           autocomplete="Nombre" autofocus>

                                                    @error('Nombre')
                                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label for="password" class="">{{ __('Password') }}</label>
                                                    <input id="password" type="password"
                                                           class="form-control @error('password') is-invalid @enderror"
                                                           name="password" required autocomplete="current-password">

                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remember"
                                                               id="remember" {{ old('remember') ? 'checked' : '' }}>

                                                        <label class="form-check-label" for="remember">
                                                            {{ __('Remember Me') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-success" style="border-radius: 0.5em">
                                                        <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
                                                    </button>

                                                    @if (Route::has('password.request'))
                                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                                            {{ __('Forgot Your Password?') }}
                                                        </a>
                                                    @endif
                                                    <p style="margin-top: 15px; margin-bottom: -0.5em">En caso de problemas, contactar al <a href="mailto:primobanano@valad.net">administrador.</a></p>
                                                </div>
                                            </div>
                                        </form>
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
        if (document.getElementById) {
            window.onload = swap
        }

        function swap() {
            var numimages = 6;
            rndimg = new Array("/img/DSCN1606.jpg", "/img/DSCN1402.jpg", "/img/DSCN1825.jpg", "/img/DSCN2673.jpg", "/img/DSCN2674.jpg", "/img/DSCN2690.jpg");
            x = (Math.floor(Math.random() * numimages));
            randomimage = (rndimg[x]);
            document.getElementById("bg-image").style.backgroundImage = "url(" + randomimage + ")";
        }
    </script>
    @endpush
@endsection
