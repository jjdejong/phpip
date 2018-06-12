<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'phpIP') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">  -->

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'phpIP') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
                            <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            <!-- <li><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li> -->
                        @else
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                  Matters  <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="{{ url('/matter/') }}">All</a>
                                    <a class="dropdown-item" href="{{ url('/matter?display_with=PAT') }}">Patents</a>
                                    <a class="dropdown-item" href="{{ url('/matter?display_with=TM') }}">Trademarks</a>
                                    <a class="dropdown-item" href="#newMatterModal" data-toggle="modal">New</a>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                  Tables  <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="{{ url('/rule/') }}">Edit rules</a>
                                    <a class="dropdown-item" href="{{ url('/eventname/') }}">Edit event names</a>
                                    <a class="dropdown-item" href="{{ url('/actor/') }}">Actors</a>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="container">
            @yield('content')
        </main>
    </div>

    <div id="newMatterModal" class="modal fade" role="dialog">
    	<div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
    	    <div class="modal-header">
    				<h4 class="modal-title">Create Matter</h4>
    				<button type="button" class="close" data-dismiss="modal">&times;</button>
    			</div>
    			<div class="modal-body">
    				@include('matter.create', ['operation' => 'new'])
    			</div>
    			<div class="modal-footer">
    				<span class="alert float-left"></span>
    				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    			</div>
        </div>
    	</div>
    </div>
    @yield('script')
</body>
</html>
