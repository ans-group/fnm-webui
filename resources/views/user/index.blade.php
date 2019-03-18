@extends('layouts.app')

@section('title')
User Management
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">User Management</h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('users.create') }}" class="btn btn-success"><i class="fa fa-user-plus" aria-hidden="true"></i> &nbsp; Add User</a>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-info">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
                <br>
            @endif

            <table class="table table-striped" id="datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Active</th>
                        <th class="text-center">Notify</th>
                        <th class="text-center">Admin</th>
                        <th>Last Login</th>
                        <th>Last IP</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td><strong>{{ $user->id }}</strong></td>
                            <td>{{ $user->name }}</td>
                            <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                            <td class="text-center">
                                @if ($user->active)
                                    <i class="fa fa-check text-success" aria-hidden="true"></i>
                                    <span class="hidden">active</span>
                                @else
                                    <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($user->notify)
                                    <i class="fa fa-check text-success" aria-hidden="true"></i>
                                    <span class="hidden">admin</span>
                                @else
                                    <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                    <span class="hidden">regular</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($user->admin)
                                    <i class="fa fa-check text-success" aria-hidden="true"></i>
                                    <span class="hidden">admin</span>
                                @else
                                    <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                    <span class="hidden">regular</span>
                                @endif
                            </td>
                            <td>{{ $user->last_login_time }}</td>
                            <td>{{ $user->last_login_ip }}</td>
                            <td class="text-right">
                                <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="btn btn-xs btn-default"><i class="fa fa-pencil" aria-hidden="true"></i> &nbsp;Edit User</a> &nbsp;
                                <a href="{{ $user->id === Auth::user()->id ? '#' : route('users.toggle', $user) }}" class="btn btn-xs {{ $user->active ? 'btn-info' : 'btn-warning' }}" {{$user->id === Auth::user()->id ? 'disabled' : ''}}><i class="fa {{ $user->active ? 'fa-toggle-on' : 'fa-toggle-off' }}" aria-hidden="true"></i> &nbsp;{{ $user->active ? 'Disable' : 'Enable' }} user</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
