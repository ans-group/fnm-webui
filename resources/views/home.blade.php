@extends('layouts.app')

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Error processing request:</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    @if(Auth::user()->admin)
    <!-- Quick Ban -->
    <div class="row">
        <div class="col-md-5 col-xs-12 col-sm-6">
            <div class="well well-sm">
                <form class="form-inline text-center" role="form" method="POST" action="{{ url('blackhole') }}" onsubmit="return confirm('*** Are you sure you want to ban this IP? ***\n\nManual bans will not be automatically removed.');">
                  <fieldset>
                    <legend>Quick Ban &nbsp; <small><a href="#" data-toggle="tooltip" data-placement="bottom" title="This creates a manual ban which does not expire automatically."><i class="fa fa-info-circle text-info"></i></a></small></legend>
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <input type="text" class="form-control input-lg" id="unban-ip" name="ip" placeholder="1.1.1.1" required> &nbsp;
                        <button type="submit" class="btn btn-lg btn-primary"><i class="fa fa-minus-circle" aria-hidden="true"></i> &nbsp; <strong>BAN IP</strong></button>
                      <br class="clear">
                    </div>
                 </fieldset>
                </form>
            </div>
        </div>

        <div class="col-md-2 hidden-sm hidden-xs text-center">
            <img src="/img/banhammer.jpg" height="118px" alt="The whitespace needed to be filled..." />
        </div>

        <!-- Quick Unban -->
        <div class="col-md-5 col-xs-12 col-sm-6">
            <div class="well well-sm">
                <form class="form-inline text-center" role="form" method="POST" action="{{ url('blackhole') }}">
                  <fieldset>
                    <legend>Quick Unban &nbsp; <small><a href="#" data-toggle="tooltip" data-placement="bottom" title="This removes an existing ban. If the IP is still over the threshold, it will be re-banned automatically."><i class="fa fa-info-circle text-info"></i></a></small></legend>
                    @csrf
                    @method('DELETE')
                    <div class="form-group">
                        <input type="text" class="form-control input-lg" id="unban-ip" name="ip" placeholder="1.1.1.1" required> &nbsp;
                        <button type="submit" class="btn btn-lg btn-success"><i class="fa fa-check-circle" aria-hidden="true"></i> &nbsp; <strong>UNBAN IP</strong></button>
                    </div>
                 </fieldset>
                </form>
            </div>
        </div>
    </div>
    @endif


    <div class="row equal">
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="panel panel-default panel-100">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        System Overview <small class="text-muted"><em>(cached for 1m)</em></small>
                        <i id="overview-panel-loader" class="fa fa-spinner fa-pulse pull-right hidden"></i>
                    </h3>
                </div>
                @if(count($dcs) == 0)
                <br>
                <p><center>No DCs configured</center></p>
                @else
                <table class="table table-stats">
                    <thead>
                        <tr>
                            <th>Instance</th>
                            <th>Bans</th>
                            <th>Unbans</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach($dcs as $dc)
                            <tr>
                                <td><a href="{{ route('dc.show', $dc) }}">{{ $dc->name }}</a>     @if ($dc->license()['licensed_bandwidth'] === 0 || $dc->license()['expiration_date_carbon'] < \Carbon\Carbon::now()) &nbsp; <i class="fa fa-exclamation-triangle text-warning" data-toggle="tooltip" data-placement="right" title="This instance is not licensed!"></i> @endif</td>
                                @if($dc->banStatus())
                                <td class="text-success" id="{{$dc->id}}_bans_enabled"><i class="fa fa-check" aria-hidden="true"></i></td>
                                @else
                                <td class="text-danger" id="{{$dc->id}}_bans_enabled"><i class="fa fa-times" aria-hidden="true"></i></td>
                                @endif
                                @if($dc->unbanStatus())
                                <td class="text-success" id="{{$dc->id}}_unbans_enabled"><i class="fa fa-check" aria-hidden="true"></i></td>
                                @else
                                <td class="text-danger" id="{{$dc->id}}_unbans_enabled"><i class="fa fa-times" aria-hidden="true"></i></td>
                                @endif
                            </tr>
                            @endforeach
                    </tbody>
                </table>

                <br>

                <table class="table table-stats-right">
                    <thead>
                        <tr>
                            <th>Instance</th>
                            <th>Inbound</th>
                            <th>Outbound</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dcs as $dc)
                        <tr>
                            <td><a href="{{ route('dc.show', $dc) }}">{{ $dc->name }}</a></td>
                            <td id="{{$dc->id}}_in_mbps">{{ round($dc->totalTraffic()['in_mbps']) }} {{ $dc->totalTraffic()['in_mbps_suffix'] }}</td>
                            <td id="{{$dc->id}}_out_mbps">{{ round($dc->totalTraffic()['out_mbps']) }} {{ $dc->totalTraffic()['out_mbps_suffix'] }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td id="{{$dc->id}}_in_pps">{{ round($dc->totalTraffic()['in_pps']) }} {{ $dc->totalTraffic()['in_pps_suffix'] }}</td>
                            <td id="{{$dc->id}}_out_pps">{{ round($dc->totalTraffic()['out_pps']) }} {{ $dc->totalTraffic()['out_pps_suffix'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <br>

                <table class="table table-stats-right">
                    <thead>
                        <tr>
                            <th>Instance</th>
                            <th>Banned</th>
                            <th>Warned</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dcs as $dc)
                        <tr>
                            <td><a href="{{ route('dc.show', $dc) }}">{{ $dc->name }}</a></td>
                            <td>{{ count($dc->getBlackholes()) }}</td>
                            <td>N/A</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="panel panel-default panel-100">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Network Traffic <small class="text-muted"><em>(cached for 1m)</em></small>
                        <i id="network-panel-loader" class="fa fa-spinner fa-pulse pull-right hidden"></i>
                    </h3>
                </div>
                @if(count($dcs) == 0)
                <br>
                <p><center>No DCs configured</center></p>
                @else
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
                        @foreach($hostTraffic->take(12) as $h)
                        <tr>
                            <td><a href="{{ route('ip.find') }}?q={{ urlencode($h['host']) }}">{{ $h['host'] }}</a></td>
                            <td>{{ $h['incoming_packets'] }}</td>
                            <td>{{ round($h['incoming_bytes'] / 102400) }}</td>
                            <td>{{ $h['incoming_flows'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div class="col-lg-5 col-sm-6 col-xs-12">
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
                            <th>DC</th>
                            <th>UUID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($blackholes) == 0)
                        <tr><td colspan="3"><center>No banned IPs</center></td></tr>
                        @else
                            @foreach($blackholes as $b)
                            <tr>
                                <td><strong><a href="{{ route('action.index',['uuid' => $b['uuid']]) }}">{{ $b['ip'] }}</a></strong></td>
                                <td><strong><a href="{{ route('dc.show', $b['dc_id']) }}">{{ $b['dc_name'] }}</a></strong></td>
                                <td class="text-right"><kbd>{{ $b['uuid'] }}</kbd></td>
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
                    @if(count($actions) == 0)
                    @else
                        @foreach($actions as $i)
                        <tr>
                            <td>{{ $i->created_at }}</td>
                            <td><a href="{{ route('dc.show', $i->dc)}}">{{ $i->dc->name }}</a></td>
                            @if($i->attack_detection_source == "manual")
                                <td>Manually {{ $i->action }}ned {{ $i->ip }}</td>
                            @else
                                <td>{{ ucfirst($i->action) }}ned {{ $i->ip }} at {{ $i->attack_peak_power }} pps</td>
                            @endif
                            <td class="text-right"><a href="{{ route('action.show', $i) }}" data-toggle="tooltip" data-placement="left" title="View attack details"><i class="fa fa-search-plus" aria-hidden="true"></i></a></td>
                        </tr>
                        @endforeach
                    @endif
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
