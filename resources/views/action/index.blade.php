@extends('layouts.app')

@section('title')
Action History
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Action History</h2>
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
                    <strong>Note:</strong> The page below has been filtered based on your search. &nbsp; <small><a class="text-info" href="{{ route('action.index') }}">Undo filtering.</a></small>
                </div>
            @endif

            <div class="text-center">{{ $actions->appends($_GET)->links() }}</div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Action</th>
                        <th>DC</th>
                        <th>IP</th>
                        <th>Host Group</th>
                        <th>PPS</th>
                        <th>MBPS</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($actions) == 0)
                    <tr><td colspan="8">No actions logged!</td></tr>
                    @else
                        @foreach($actions as $action)
                            <tr>
                                <td><strong><a href="{{ route('action.show', $action) }}">{{ $action->created_at }}</a></strong></td>
                                <td>{{ $action->action }}</td>
                                <td>
                                    <a href="{{ route('dc.show', $action->dc) }}">{{ $action->dc->name }}</a>
                                </td>
                                <td><kbd>{{ $action->ip }}</kbd></td>
                                <td><a href="{{ route('hostgroup.show', $action->hostgroup) }}">{{ $action->hostgroup->name }}</a></td>
                                <td>{{ $action->attack_total_incoming_pps }} pps</td>
                                <td>{{ round($action->attack_total_incoming_traffic / 1024) }} mbps</td>
                                <td class="text-right">
                                    <a href="{{ route('action.show', $action) }}" class="btn btn-xs btn-default"><i class="fa fa-search" aria-hidden="true"></i> &nbsp;View</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="text-center">{{ $actions->appends($_GET)->links() }}</div>
        </div>
    </div>
</div>
@endsection
