<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Bansis')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/colores_banano.css') }}" rel="stylesheet">
    @stack('css')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm fixed-top"
         style="background-color: #134c39 !important">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <span
                    style="font-size: 20px; font-weight: bold; text-decoration: none">{{ config('app.name', 'Laravel') }} </span>
                | Sistema de Banano
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        @php
                            $padre_id = array(1,2)
                        @endphp
                        @foreach($padre_id as $data)
                            @php $padre = $data @endphp
                            @foreach($recursos as $recurso)
                                @if(trim($recurso->PadreID) == trim($padre))
                                    <li class="nav-item dropdown">
                                        <a id="navbarDropdown1" class="nav-link dropdown-toggle" href="#" role="button"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                            {{$recurso->Nombre}}
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right"
                                             aria-labelledby="navbarDropdown1">
                                            @foreach($recursos as $recurso1)
                                                @if(trim($recurso1->PadreID) == trim( $recurso->ID))
                                                    <a class="dropdown-item disabled" href="#">
                                                        {{$recurso1->Nombre}}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    @foreach($recursos as $recurso2)
                                                        @if(trim($recurso2->PadreID) == trim($recurso1->ID))
                                                            <a class="dropdown-item"
                                                               href="{{url('/sistema/'.strtolower($recurso2->modulo).'/'.strtolower($recurso2->objeto).'/'.$recurso2->ID)}}">
                                                                {{explode('-',$recurso2->Nombre)[1]}}
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                    <div class="dropdown-divider"></div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        @endforeach

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown1" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                EMPACADORA
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown1">
                                <a class="dropdown-item" href="{{route('empacadora.cajas')}}">
                                    CAJAS
                                </a>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ strtoupper(Auth::user()->Nombre) }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <main style="margin-top: 5rem">
        @yield('content')
        @include('layouts.modal')
    </main>
</div>
@stack('scripts')
</body>
</html>
