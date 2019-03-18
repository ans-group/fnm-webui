@extends('layouts.app')

@section('title')
Create User
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Edit User &nbsp;<small>&ndash; {{$user->name}}</small></h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('users.index') }}" class="btn btn-default"><i class="fa fa-users" aria-hidden="true"></i> &nbsp; List Users</a>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Error validating input:</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">User Configuration</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="control-label">Full Name:</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" placeholder="John Smith" required>
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="name" class="control-label">Email Address: &nbsp; <small><em>(used to login)</em></small></label>
                            <input type="text" class="form-control" name="email" value="{{ old('email', $user->email) }}" placeholder="john.smith@example.com" required>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Password:</label>
                            <input type="password" class="form-control" name="password" placeholder="Edit to change...">
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password_confirmation" class="control-label">Confirm Password:</label>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm edit to change...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">User Permissions</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="active" class="control-label">Enabled:</label>
                            <select name="active" class="form-control">
                                <option value="1" {{ old('active', $user->active)=='1' ? ' selected' : '' }}>Yes</option>
                                <option value="0" {{ old('active', $user->active)=='0' ? ' selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="form-group{{ $errors->has('notify') ? ' has-error' : '' }}">
                            <label for="notify" class="control-label">Notify about new actions:</label>
                            <select name="notify" class="form-control">
                                <option value="0" {{ old('notify', $user->notify)=='0' ? ' selected' : '' }}>No</option>
                                <option value="1" {{ old('notify', $user->notify)=='1' ? ' selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div class="form-group{{ $errors->has('admin') ? ' has-error' : '' }}">
                            <label for="admin" class="control-label">Group:</label>
                            <select name="admin" class="form-control">
                                <option value="0" {{ old('admin', $user->admin)=='0' ? ' selected' : '' }}>Read Only (can view metrics, but cannot edit settings or ban IPs)</option>
                                <option value="1" {{ old('admin', $user->admin)=='1' ? ' selected' : '' }}>Super Administrator (can view and edit all apsects of the panel, and can ban IPs)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary" type="submit">Update User</button>
            </div>
            <div class="col-md-6 text-right">
                @if($user->active)
                <p class="test-muted">User must be disabled before deleting.</p>
                @else
                <a href="{{ route('users.delete', $user) }}" class="btn btn-warning">Delete User</a>
                @endif
            </div>

        </form>

    </div>
</div>
@endsection
