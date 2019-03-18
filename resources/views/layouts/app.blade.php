<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="/favicon.ico">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FastNetMon') }} - @yield('title', 'Management Console')</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bootswatch/3.3.7/simplex/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/fontawesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ url('/css/main.css') }}">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>

<div id="app">

        <nav class="navbar navbar-default navbar-fixed-top">
              <div class="container">
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand navbar-brand-custom" href="{{ url('/') }}">
                      <i class="fa fa-gavel text-danger" aria-hidden="true"></i> &nbsp;<strong>FNM</strong>
                  </a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                @if (Auth::guest())
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{ route('login') }}"><i class="fa fa-lock" aria-hidden="true"></i> Sign in</a></li>
                </ul>
                @else
                  <!-- Custom navigation goes here -->
                  @yield('nav')
                  <!-- End custom navigation -->

              <ul class="nav navbar-nav navbar-left">
                  <li class="dropdown">
                      <a href="#" lass="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-hospital-o" aria-hidden="true"></i> &nbsp; DCs &nbsp;<span class="caret"></span></a>

                      <ul class="dropdown-menu">
                          @foreach(App\DC::where('active', 1)->take(5)->get() as $navDC)
                          <li><a href="{{ route('dc.show', [$navDC]) }}">{{ $navDC->name }}</a></li>
                          @endforeach
                          @if(App\DC::where('active', 1)->count() > 5)
                          <li role="separator" class="divider"></li>
                          <li><a href="{{ route('dc.index') }}"><strong>View all &rarr;</strong></a></li>
                          @endif
                          @if(Auth::user()->admin)
                              <li role="separator" class="divider"></li>
                              <li class="dropdown-header">Admin Actions</li>
                              <li><a href="{{ route('dc.index') }}">Manage Datacenters</a></li>
                              <li><a href="{{ route('dc.create') }}">Create Datacenter</a></li>
                          @endif
                      </ul>
                  </li>

                  <li class="dropdown">
                      <a href="#" lass="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-globe" aria-hidden="true"></i> &nbsp; IPs &nbsp;<span class="caret"></span></a>

                      <ul class="dropdown-menu">
                          <li><a href="{{ route('hostgroup.index') }}">View Hostgroups</a></li>
                          <li><a href="{{ route('ip.index') }}">View IPs</a></li>
                          @if(Auth::user()->admin)
                              <li role="separator" class="divider"></li>
                              <li class="dropdown-header">Admin Actions</li>
                              <li><a href="{{ route('hostgroup.create') }}">Create Hostgroup</a></li>
                              <li><a href="{{ route('ip.create') }}">Create IP range</a></li>
                          @endif
                      </ul>
                  </li>

                  <li><a href="{{ route('action.index') }}"><i class="fa fa-bullhorn" aria-hidden="true"></i> &nbsp; Actions</a></li>

                  @if(Auth::user()->admin)
                  <li><a href="{{ route('users.index') }}"><i class="fa fa-users" aria-hidden="true"></i> &nbsp; Users</a></li>
                  @endif
              </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{ route('app_changelog') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp; Changelog</a></li>
                    <li><a href="{{ env('BUG_URL', 'https://github.com/ukfast/fnm-webui/issues') }}" target="_blank"><i class="fa fa-bug" aria-hidden="true"></i>&nbsp; Report a bug</a></li>

                    <!-- User settings button -->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user-circle-o" aria-hidden="true"></i>&nbsp; {{ Auth::user()->name }} &nbsp;<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('users.password') }}">Change password</a></li>
                            <li role="separator" class="divider"></li>
                            <li class="signout">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <strong>Sign out</strong></a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                @endif
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    @if (session('connection-error'))
        <div class="container">
            <br>
            <div class="alert alert-danger">
                <i class="fa fa-plug huge pull-left"></i>
                <h4 class="title text-uppercase"><strong>Connection Error</strong></h4>
                <kbd>{{ session('connection-error')['dc'] }}</kbd> &nbsp; &ndash; &nbsp; <code>{{ session('connection-error')['error'] }}</code>
            </div>
        </div>
    @endif

    @yield('content')

    <div class="container">
        <div class="row">
            <hr>
            <div class="col-sm-6 text-left text-muted">
                Released under GPLv3. &nbsp; <a href="https://github.com/ukfast/fnm-webui">View on GitHub.</a><br>
            </div>
            <div class="col-sm-6 text-right text-muted">
                    <p>Rendered at <strong>{{ date("Y-m-d H:i:s", time()) }}</strong> in <strong>{{ round(microtime(true) - LARAVEL_START, 3) }}s</strong></p>
            </div>
        </div>
    </div>

    <br class="clear">

</div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootbox@4.4.0/bootbox.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs@1.10.19/js/dataTables.bootstrap.min.js"></script>
        <script src="{{ url('/js/main.js') }}"></script>

        @yield('js')
    </body>
    </html>
