@extends('layouts.app')

@section('title')
Host Group Management
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Host Group Management</h2>
        </div>
        <div class="col-md-6 text-right">
            @if(Auth::user()->admin)<a href="{{ route('hostgroup.create') }}" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp; Add Host Group</a>@endif
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
                    <strong>Note:</strong> The page below has been filtered based on your search. &nbsp; <small><a class="text-info" href="{{ route('hostgroup.index') }}">Undo filtering.</a></small>
                </div>
                <br>
            @endif

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>DC</th>
                        <th>Name</th>
                        <th>PPS Threshold</th>
                        <th>MBPS Threshold</th>
                        <th class="text-center">Ban Enabled</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($hgs) == 0)
                    <tr><td colspan="6">No host groups configured. &nbsp; <a href="{{ route('hostgroup.create') }}">Add one?</a></td></tr>
                    @else
                        @foreach($hgs as $hg)
                            <tr>
                                <td><strong><a href="{{ route('hostgroup.show', $hg) }}">{{ $hg->id }}</a></strong></td>
                                <td>
                                    <a href="{{ route('dc.show', $hg->dc) }}">{{ $hg->dc->name }}</a>
                                    @if (!$filtered) &nbsp; <small><a class="text-muted" href="{{ route('hostgroup.index') }}?dc={{ $hg->dc->id}}" data-toggle="tooltip" data-placement="right" title="Filter list to {{ $hg->dc->name }}"><i class="fa fa-filter" aria-hidden="true"></i></a></small>@endif
                                </td>
                                <td>{{ $hg->name }}</td>
                                <td>{{ $hg->meta()['threshold_pps'] }} pps</td>
                                <td>{{ $hg->meta()['threshold_mbps'] }} mbps</td>
                                <td class="text-center">
                                    @if ($hg->meta()['enable_ban'])
                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                    @else
                                        <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('hostgroup.show', $hg) }}" class="btn btn-xs btn-default"><i class="fa fa-search" aria-hidden="true"></i> View</a>&nbsp;
                                    @if(Auth::user()->admin)<a href="{{ route('hostgroup.edit', $hg) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>@endif
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
