@extends('layouts.app')

@section('title')
Update Password
@endsection

@section('content')
<div class="container">

    <br class="clear hidden-sm hidden-xs" />

    <div class="col-md-6 col-md-offset-3">
        <div class="well lower-box">

                <form class="form-horizontal" role="form" method="POST" action="">
                        @csrf

                        <legend><i class="fa fa-key" aria-hidden="true"></i> &nbsp; Change Password</legend>

                        @if (session('status'))
                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>{{ session('status') }}</strong>
                            </div>
                        @endif

                        <div class="form-group{{ $errors->has('original_password') ? ' has-error' : '' }}">
                            <label for="original_password" class="col-md-4 control-label">Current Password</label>

                            <div class="col-md-6">
                                <input id="original_password" type="password" class="form-control" name="original_password" value="" required autofocus>

                                @if ($errors->has('original_password'))
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $errors->first('original_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-md-4 control-label">New Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                <div class="alert alert-dismissible alert-warning">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <strong>{{ $errors->first('password') }}</strong>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password2-confirm" class="col-md-4 control-label">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-password2" type="password" class="form-control" name="password2" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
        </div>
    </div>
</div>
@endsection
