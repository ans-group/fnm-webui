@extends('layouts.app')

@section('title')
DC Management
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">DC Management</h2>
        </div>
        <div class="col-md-6 text-right">
            @if(Auth::user()->admin)<a href="{{ route('dc.create') }}" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp; Add DC</a>@endif
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
                <br>
            @endif

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>API URL</th>
                        <th class="text-center">Active</th>
                        <th class="text-center">API Online</th>
                        <th class="text-center">Licensed</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($dcs) == 0)
                    <tr><td colspan="6">No DCs configured. &nbsp; <a href="{{ route('dc.create') }}">Add one?</a></td></tr>
                    @else
                        @foreach($dcs as $dc)
                            <tr>
                                <td><strong><a href="{{ route('dc.show', [$dc]) }}">{{ $dc->id }}</a></strong></td>
                                <td>{{ $dc->name }}</td>
                                <td><kbd>{{ $dc->api_url }}</kbd></td>
                                <td class="text-center">
                                    @if ($dc->active)
                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                        <span class="hidden">active</span>
                                    @else
                                        <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($dc->active && $dc->online() === 1)
                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                        <span class="hidden">active</span>
                                    @elseif($dc->active && $dc->online() === 0)
                                        <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                    @else
                                        <i class="fa fa-question text-info" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="API has not been polled yet, or this DC is disabled."></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($dc->license()['licensed_bandwidth'] === 0 || $dc->license()['expiration_date_carbon'] < \Carbon\Carbon::now())
                                        <i class="fa fa-times text-danger" aria-hidden="true"></i> <!-- Not licensed -->
                                    @else
                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('dc.show', [$dc]) }}" class="btn btn-xs btn-default"><i class="fa fa-cogs" aria-hidden="true"></i> &nbsp;Manage DC</a> &nbsp;
                                    @if(Auth::user()->admin)<a href="{{ route('dc.edit', [$dc]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil" aria-hidden="true"></i> &nbsp;Edit DC</a>@endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
