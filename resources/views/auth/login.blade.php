@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row visible-sm visible-xs">
        <div class="col-xs-12">
            <br class="clear" />
            <br class="clear" />
            <div class="alert alert-dismissible alert-info">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Be warned, mobile user!</strong><br>This application is not designed for use on mobile devices. For best results use Chrome on Windows.
            </div>
        </div>
    </div>

    <div class="row">

        <br class="clear hidden-sm hidden-xs" />

        <div class="col-md-6 col-md-offset-3">
            <div class="well lower-box">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                <fieldset>
                    <legend><i class="fa fa-user-circle" aria-hidden="true"></i> &nbsp; Authentication required</legend>
                    @if (count($errors) == 1)
                        <div class="alert alert-danger">
                            <strong>Unable to login.</strong>&nbsp; {{ $errors->first() }}
                        </div>
                    @endif

                    @if (count($errors) > 1)
                        <div class="alert alert-danger">
							<strong>The following errors were encountered:</strong>
							<ul>
								@foreach ($errors->all() as $error)
		                        <li>{{ $error }}</li>
								@endforeach
							</ul>
                        </div>
                    @endif

                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">Email</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required {{ $errors->has('email') ? '' : 'autofocus' }}>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password')||$errors->has('email') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">Password</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" name="password" required {{ $errors->has('email') ? 'autofocus' : '' }}>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <br>
@endsection
