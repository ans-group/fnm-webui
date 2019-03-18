@extends('layouts.app')

@section('title')
IP Management
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">IP Management</h2>
        </div>
        <div class="col-md-6 text-right">
            @if(Auth::user()->admin)<a href="{{ route('ip.create') }}" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp; Add IP Range</a>@endif
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

            @if ($filtered)
                <div class="alert alert-info">
                    <strong>Note:</strong> The page below has been filtered based on your search. &nbsp; <small><a class="text-info" href="{{ route('ip.index') }}">Undo filtering.</a></small>
                </div>
            @endif

            <div class="text-center">{{ $ips->appends($_GET)->links() }}</div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>DC</th>
                        <th>IP Range</th>
                        <th>Host Group</th>
                        <th>Notes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($ips) == 0)
                    <tr><td colspan="6">No IP ranges configured. &nbsp; <a href="{{ route('ip.create') }}">Add one?</a></td></tr>
                    @else
                        @foreach($ips as $ip)
                            <tr>
                                <td><strong><a href="{{ route('ip.show', $ip) }}">{{ $ip->id }}</a></strong></td>
                                <td>
                                    <a href="{{ route('dc.show', $ip->dc) }}">{{ $ip->dc->name }}</a>
                                    @if (!$filtered) &nbsp; <small><a class="text-muted" href="{{ route('ip.index') }}?dc={{ $ip->dc->id}}" data-toggle="tooltip" data-placement="right" title="Filter list to {{ $ip->dc->name }}"><i class="fa fa-filter" aria-hidden="true"></i></a></small>@endif
                                </td>
                                <td><kbd>{{ $ip->range }}</kbd></td>
                                <td><a href="{{ route('hostgroup.show', $ip->hostgroup) }}">{{ $ip->hostgroup->name }}</a></td>
                                <td>{{ $ip->description ? $ip->description : 'None set'}}</td>
                                <td class="text-right">
                                    <!-- <a href="{{ route('ip.show', $ip) }}" class="btn btn-xs btn-default"><i class="fa fa-search" aria-hidden="true"></i> &nbsp;View</a> -->
                                    @if(Auth::user()->admin)&nbsp;<a href="{{ route('ip.edit', $ip) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil" aria-hidden="true"></i> &nbsp;Edit</a>@endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            <div class="text-center">{{ $ips->appends($_GET)->links() }}</div>
        </div>
    </div>
</div>
@endsection
