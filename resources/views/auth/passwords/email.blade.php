@extends('layouts.app')

@section('content')
<div class="container">

    <br class="clear hidden-sm hidden-xs" />

    <div class="col-md-6 col-md-offset-3">
        <div class="well lower-box">

                <form class="form-horizontal" role="form" method="POST"  action="{{ route('password.email') }}">
                        @csrf

                        <legend><i class="fa fa-key" aria-hidden="true"></i> &nbsp; {{ __('Reset Password') }}</legend>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                <strong>{{ session('status') }}</strong>
                            </div>
                        @endif

                        @if ($errors->has('email'))
                            <div class="alert alert-warning" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                        @endif

                        @csrf

                        <div class="form-group {{ $errors->has('email') ? ' has-errors' : '' }}">
                            <label for="email" class="col-md-4 control-label">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
        </div>
    </div>
</div>
@endsection
