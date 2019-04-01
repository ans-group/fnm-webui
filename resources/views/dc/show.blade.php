@extends('layouts.app')

@section('title')
Manage DC - {{$dc->name}}
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Manage DC - <small>{{ $dc->name }}</small></h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('dc.index') }}" class="btn btn-link">&larr; All DCs</a>&nbsp;
            @if($dc->description)<a href="#" class="btn btn-md btn-danger" data-toggle="hoverpopover" data-placement="left" title="<strong>DC Description</strong>" data-content="{!! nl2br($dc->description) !!}"><i class="fa fa-sticky-note"></i> &nbsp; Notes</a>@endif
            @if(Auth::user()->admin) &nbsp; <a href="{{ route('dc.edit', [$dc]) }}" class="btn btn-default"><i class="fa fa-pencil" aria-hidden="true"></i> &nbsp; Edit Configuration</a>@endif
        </div>
    </div>

    <br>

    @if (!$dc->active)
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-dismissable alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>This DC is disabled.</strong> Data from this DC will not appear throughout the application. @if(Auth::user()->admin) &nbsp; <a href="{{ route('dc.edit', [$dc]) }}">Update configuration &rarr;</a>@endif
            </div>
        </div>
    </div>
    @endif

    @if ($dc->license()['licensed_bandwidth'] === 0 || $dc->license()['expiration_date_carbon'] < \Carbon\Carbon::now())
        @component('components.licensewarn')
        @endcomponent
    @endif

    <div class="row">

        <div class="col-sm-3">
            <div class="panel {{ $dc->banStatus() ? 'panel-success' : 'panel-primary' }}">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-pause-circle fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">Bans</div>
                            <div><strong>{{ $dc->banStatus() ? 'ENABLED' : 'DISABLED' }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel {{ $dc->unbanStatus() ? 'panel-success' : 'panel-primary' }}">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-play-circle fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">Unbans</div>
                            <div><strong>{{ $dc->unbanStatus() ? 'ENABLED' : 'DISABLED' }}</strong></div>
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
                            <i class="fa fa-bar-chart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ round($dc->totalTraffic()['in_mbps']) }} {{ $dc->totalTraffic()['in_mbps_suffix'] }}</div>
                            <div><strong>INBOUND BYTES</strong></div>
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
                            <div class="huge">{{ round($dc->totalTraffic()['in_pps']) }} {{ $dc->totalTraffic()['in_pps_suffix'] }}</div>
                            <div><strong>INBOUND PACKETS</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-dismissable alert-info">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Success!</strong> {{ session('success') }}
            </div>
        </div>
    </div>
    @endif

    <div class="row equal">

        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="panel panel-default panel-100">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Management Actions
                    </h3>
                </div>
                <div class="panel-body">
                    @if(Auth::user()->admin)
                        <p><a href="{{ route('dc.toggleban', $dc) }}" class="btn btn-block btn-default loading"><strong>{{ $dc->banStatus() ? 'Disable' : 'Enable' }}</strong> automated banning</a></p>
                        <p><a href="{{ route('dc.toggleunban', $dc) }}" class="btn btn-block btn-default loading"><strong>{{ $dc->unbanStatus() ? 'Disable' : 'Enable' }}</strong>  automated unbanning</a></p>
                        <hr>
                        <p><a href="{{ route('hostgroup.index') }}?dc={{ $dc->id }}" class="btn btn-block btn-default">Manage hostgroups</a></p>
                        <p><a href="{{ route('ip.index') }}?dc={{ $dc->id }}" class="btn btn-block btn-default">Manage IP ranges</a></p>
                    @else
                        <p class="text-center text-muted">
                            <br><br>
                            <i class="fa fa-user-secret fa-5x"></i><br>
                            <strong>You are not an administrator</strong><br>
                            There is nothing to see here
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-sm-6 col-xs-12">
            <div class="panel panel-default panel-100">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Network Traffic <small class="text-muted"><em>(cached for 1m)</em></small>
                        <i id="network-panel-loader" class="fa fa-spinner fa-pulse pull-right hidden"></i>
                    </h3>
                </div>
                <table class="table table-striped table-stats-right">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>PPS</th>
                            <th>MBPS</th>
                            <th>Flows</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($dc->hostTraffic() == false || count($dc->hostTraffic()) == 0)
                        <tr><td colspan="4"><center>No traffic data received from instance</center></td></tr>
                        @else
                            @foreach($dc->hostTraffic() as $h)
                            <tr>
                                <td><a href="{{ route('ip.find') }}?q={{ urlencode($h['host']) }}">{{ $h['host'] }}</a></td>
                                <td>{{ $h['incoming_packets'] }}</td>
                                <td>{{ round($h['incoming_bytes'] / 102400) }}</td>
                                <td>{{ $h['incoming_flows'] }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="panel panel-default panel-100">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Banned IPs <small class="text-muted"><em>(cached for 1m)</em></small>
                        <i id="network-panel-loader" class="fa fa-spinner fa-pulse pull-right hidden"></i>
                    </h3>
                </div>
                <table class="table table-striped table-stats-right">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($dc->getBlackholes() == false || count($dc->getBlackholes()) == 0)
                        <tr><td colspan="2"><center>No banned IPs</center></td></tr>
                        @else
                            @foreach($dc->getBlackholes() as $b)
                            <tr>
                                <td><strong><a href="{{ route('action.index',['uuid' => $b['uuid']]) }}">{{ $b['ip'] }}</a></strong></td>
                                <td class="text-right"><a href="{{ route('action.index',['uuid' => $b['uuid']]) }}" data-toggle="tooltip" data-placement="left" title="View attack details"><i class="fa fa-search-plus" aria-hidden="true"></i></a></td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <br>

    <div class="row equal">
        <div class="col-md-6">
            <div class="panel panel-default panel-100">
                <div class="panel-heading"><h3 class="panel-title">Recent Actions</h3></div>
                <table class="table table-striped">
                    <tbody>
                        @foreach(\App\Actions::all()->sortByDesc('id')->where('dc_id', $dc->id)->take(10) as $i)
                        <tr>
                            <td>{{ $i->created_at }}</td>
                            <td><a href="{{ route('dc.show', $i->dc)}}">{{ $i->dc->name }}</a></td>
                            @if($i->attack_detection_source == "manual")
                                <td>Manually {{ $i->action }}ned {{ $i->ip }}</td>
                            @else
                                <td>{{ ucfirst($i->action) }}ned {{ $i->ip }} at {{ round($i->attack_peak_power / 1024, 2) }} kpps</td>
                            @endif
                            <td class="text-right"><td class="text-right"><a href="{{ route('action.show', $i) }}" data-toggle="tooltip" data-placement="left" title="View attack details"><i class="fa fa-search-plus" aria-hidden="true"></i></a></td></td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="text-center"><a href="{{ route('action.index', ['dc' => $dc->id]) }}">View all actions for {{ $dc->name }} &rarr;</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default panel-100">
                <div class="panel-heading"><h3 class="panel-title">Attack Summary</h3></div>
                <div class="panel-body">
                    This box will contain a table of the most frequent attack types logged by FNM.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
setTimeout(function(){
   window.location.reload(1);
}, 30000);
</script>
@endsection
