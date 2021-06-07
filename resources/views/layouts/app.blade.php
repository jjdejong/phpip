<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'phpIP') }}</title>

  <!-- Scripts -->
  <script src="{{ mix('js/app.js') }}" defer></script>
  <script src="{{ asset('js/extra.js') }}" defer></script>

  <!-- Styles -->
  <link href="{{ mix('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('bootstrap-icons.css') }}" rel="stylesheet">
  @yield('style')
  @canany(['client', 'readonly'])
    <style>
      input.noformat {
        pointer-events: none;
      }
    </style>
  @endcanany
  @livewireStyles
</head>

<body>
  <div id="app">
    <nav class="navbar navbar-expand-md navbar-dark bg-primary mb-1">
      <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
          {{ config('app.name', 'phpIP') }}
        </a>
        @auth
        <form method="POST" action="/matter/search">
          @csrf
          <div class="input-group">
            <input type="search" class="form-control" id="matter-search" name="matter_search" placeholder="Search" autocomplete="off">
            <div class="input-group-append">
              <select class="custom-select" id="matter-option" name="search_field">
                <option value="Ref" selected>Case reference</option>
                <option value="Responsible">Responsible</option>
                <option value="Title">Title</option>
                <option value="Client">Client</option>
                <option value="Applicant">Applicant</option>
              </select>
              <button class="btn btn-info" type="submit">Go</button>
            </div>
          </div>
        </form>
        @endauth
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
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
            @else
            <li><a class="nav-link" href={{ route('home') }}>Dashboard</a></li>
            <li class="nav-item dropdown">
              <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Matters
              </a>
              <ul class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="{{ url('/matter') }}">All</a>
                <a class="dropdown-item" href="{{ url('/matter?display_with=PAT') }}">Patents</a>
                <a class="dropdown-item" href="{{ url('/matter?display_with=TM') }}">Trademarks</a>
                @canany(['admin', 'readwrite'])
                <li><hr class="dropdown-divider"></li>
                <a class="dropdown-item" href="/matter/create?operation=new" data-target="#ajaxModal" data-toggle="modal" data-size="modal-sm" title="Create Matter">New</a>
                @endcanany
              </ul>
            </li>
            @cannot('client')
            @canany(['admin', 'readwrite'])
            <li class="nav-item dropdown">
              <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Tools
              </a>
              <ul class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="{{ url('/renewal') }}"><i class="bi-calendar3-event"></i> Manage renewals</a>
                <a class="dropdown-item" href="{{ url('/fee') }}"><i class="bi-bank"></i> Renewal fees</a>
                @can('admin')
                <a class="dropdown-item" href="{{ url('/rule') }}"><i class="bi-calendar3-range-fill"></i> Rules</a>
                <a class="dropdown-item" href="{{ url('/template-member') }}"><i class="bi-envelope"></i> Email templates</a>
                <a class="dropdown-item" href="{{ url('/document') }}"><i class="bi-envelope-fill"></i> Email template classes</a>
                @endcan
              </ul>
            </li>
            @endcanany
            <li class="nav-item dropdown">
              <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Tables
              </a>
              <ul class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="{{ url('/actor') }}"><i class="bi-people"></i> Actors</a>
                @can('admin')
                <a class="dropdown-item" href="{{ url('/user') }}"><i class="bi-file-person"></i> DB Users</a>
                <a class="dropdown-item" href="{{ url('/eventname') }}"><i class="bi-calendar3-event-fill"></i> Event names</a>
                <li><hr class="dropdown-divider"></li>
                <a class="dropdown-item" href="{{ url('/category') }}"><i class="bi-columns"></i> Categories</a>
                <a class="dropdown-item" href="{{ url('/role') }}"><i class="bi-person-lines-fill"></i> Actor roles</a>
                <a class="dropdown-item" href="{{ url('/default_actor') }}"><i class="bi-person-badge"></i> Default actors</a>
                <a class="dropdown-item" href="{{ url('/type') }}"><i class="bi-diagram-3"></i> Matter types</a>
                <a class="dropdown-item" href="{{ url('/classifier_type') }}"><i class="bi-card-heading"></i> Classifier types</a>
                @endcan
              </ul>
            </li>
            @endcannot
            <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->login }}
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <i class="bi-box-arrow-right"></i> {{ __('Logout') }}
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
    <main class="container-fluid px-4">
      @yield('content')
      <div id="ajaxModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Ajax title placeholder</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
            <div class="modal-footer">
              <span id="footerAlert" class="alert float-left"></span>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  
  @yield('script')
  @livewireScripts
</body>
</html>
