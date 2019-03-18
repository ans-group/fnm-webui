@extends('layouts.app')

@section('title')
Manage Host Group
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Manage Host Group <small>&ndash; {{ strtolower($hg->dc->name) }}_{{ $hg->name }}</small></h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('hostgroup.index') }}" class="btn btn-link">&larr; All Host Groups</a>&nbsp;
            @if($hg->description)<a href="#" class="btn btn-md btn-danger" data-toggle="hoverpopover" data-placement="left" title="<strong>Host Group Description</strong>" data-content="{!! nl2br($hg->description) !!}"><i class="fa fa-sticky-note"></i> &nbsp; Notes</a>&nbsp;@endif
            @if(Auth::user()->admin)<a href="{{ route('hostgroup.edit', $hg) }}" class="btn btn-default"><i class="fa fa-pencil" aria-hidden="true"></i> &nbsp; Edit</a>@endif
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-sm-3">
            <div class="panel {{ $hg->meta()['enable_ban'] ? 'panel-success' : 'panel-primary' }}">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa {{ $hg->meta()['enable_ban'] ? 'fa-play-circle' : 'fa-pause-circle' }} fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">Bans</div>
                            <div><strong>{{ $hg->meta()['enable_ban'] ? 'ENABLED' : 'DISABLED' }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-line-chart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $hg->meta()['threshold_pps'] }}</div>
                            <div><strong>PPS THRESHOLD</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-area-chart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $hg->meta()['threshold_mbps'] }}</div>
                            <div><strong>MBPS THRESHOLD</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-pie-chart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $hg->meta()['threshold_flows'] }}</div>
                            <div><strong>FLOWS THRESHOLD</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title pull-left">
                        <strong>IP Ranges</strong>&nbsp; ({{ $hg->ips->count() }})
                    </div>
                    <div class="pull-right">
                        @if(Auth::user()->admin)<a href="{{ route('ip.create') }}?dc={{ $hg->dc->id }}" class="btn btn-xs btn-success"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp;Add IP Range</a>@endif
                    </div>
                    <div class="clearfix"></div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>DC</th>
                            <th>IP Range</th>
                            <th>Notes</th>
                            <!-- th></th -->
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($hg->ips) == 0)
                        <tr><td colspan="6">No IP ranges configured. &nbsp; <a href="{{ route('ip.create') }}">Add one?</a></td></tr>
                        @else
                            @foreach($ips as $ip)
                                <tr>
                                    <td><strong><a href="{{ route('ip.show', $ip) }}">{{ $ip->id }}</a></strong></td>
                                    <td><a href="{{ route('dc.show', $ip->dc) }}">{{ $ip->dc->name }}</a></td>
                                    <td><kbd>{{ $ip->range }}</kbd></td>
                                    <td>{{ $ip->description ? $ip->description : 'None set' }}</td>
                                    <!-- td class="text-right">
                                        <a href="{{ route('ip.show', $ip) }}" class="btn btn-xs btn-default"><i class="fa fa-search" aria-hidden="true"></i> &nbsp;View</a>
                                    </td -->
                                </tr>
                            @endforeach
                            @if(count($hg->ips) > 20)
                                <tr>
                                    <td colspan="4" class="text-center">{{ $ips->links() }}</td>
                                </tr>
                            @endif
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
