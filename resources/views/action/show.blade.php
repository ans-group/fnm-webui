@extends('layouts.app')

@section('title')
View Action - {{$action->uuid}}
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">View Action - <small>{{ $action->uuid }}</small></h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('action.index') }}" class="btn btn-link">&larr; All Actions</a>
            @if(!is_null($action->raw))&nbsp;<a href="#" class="btn btn-md btn-danger" data-toggle="modal" data-target=".raw_json_modal"><i class="fa fa-file-text"></i> &nbsp; Raw JSON</a>@endif
        </div>
    </div>

    <br>
    <div class="row">

        <div class="col-sm-3">
            <div class="panel {{ $action->action == "ban" ? 'panel-primary' : 'panel-success' }}">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-terminal fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ strtoupper($action->action) }}NED</div>
                            <div><strong>COMMAND</strong></div>
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
                            <i class="fa fa-hospital-o fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><a href="{{ route('dc.show', $action->dc) }}">{{ $action->dc->name }}</a></div>
                            <div><strong>INSTANCE</strong></div>
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
                            <div class="huge">{{ round($action->attack_total_incoming_traffic / 1024) }} mbps</div>
                            <div><strong>INBOUND TRAFFIC</strong></div>
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
                            <div class="huge">{{ $action->attack_total_incoming_pps }} pps</div>
                            <div><strong>INBOUND PACKETS</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row equal">
        <div class="col-md-5">
            <div class="panel panel-default panel-100">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong>Action Details</strong>
                    </div>
                </div>
                <table class="table table">
                    <tbody>
                        <tr>
                            <th>UUID</th>
                            <td><kbd>{{ $action->uuid }}</kbd></td>
                        </tr>
                        <tr>
                            <th>Timestamp</th>
                            <td>{{ $action->created_at }}</td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td>{{ $action->ip }} &nbsp; <small><a href="{{ route('ip.show', $action->ip_id) }}">[range]</a></small></td>
                        </tr>
                        <tr>
                            <th>Host Group</th>
                            <td><a href="{{ route('hostgroup.show', $action->hostgroup) }}">{{ $action->hostgroup->fullname() }}</a></td>
                        </tr>
                        <tr>
                            <th>Severity</th>
                            <td>{{ strtoupper($action->attack_severity) }}</td>
                        </tr>
                        <tr>
                            <th>Attack Type</th>
                            <td>{{ strtoupper($action->attack_type) }}</td>
                        </tr>
                        <tr>
                            <th>Attack Protocol</th>
                            <td>{{ strtoupper($action->attack_protocol) }}</td>
                        </tr>
                        <tr>
                            <th>Detection Source</th>
                            <td>{{ strtoupper($action->attack_detection_source) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-7">
            <div class="panel panel-default panel-100">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong>Similar Actions</strong> &nbsp; <em class="text-muted"><small>(for {{$action->ip}})</small></em>
                    </div>
                </div>
                <table class="table table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Command</th>
                            <th>PPS</th>
                            <th>MBPS</th>
                            <th>UUID</th>
                            <th></th>
                    </thead>
                    <tbody>
                        @if(count($similar) == 0)
                        <tr>
                            <td colspan="6" class="text-center">
                                No similar actions found
                            </td>
                        </tr>
                        @else
                            @foreach($similar as $s)
                            <tr>
                                <td>{{ $s->created_at }}</td>
                                <td><span class="label {{$s->action == 'ban' ? 'label-primary' : 'label-success'}}">{{ strtoupper($s->action) }}</span></td>
                                <td>{{ $s->attack_total_incoming_pps }}</td>
                                <td>{{ round($s->attack_total_incoming_traffic / 1024) }}</td>
                                <td><kbd>{{ $s->uuid }}</kbd></td>
                                <td class="text-right"><a href="{{ route('action.show', $s) }}" data-toggle="tooltip" data-placement="left" title="View attack details"><i class="fa fa-search-plus" aria-hidden="true"></i></a></td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="6" class="text-center">
                                    <br>
                                    <a href="{{ route('action.index', ['ip' => $action->ip]) }}">View all actions for {{ $action->ip }} &rarr;</a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br class="clear">

    @if(isset($action->packet_dump) && !is_null($action->packet_dump) && !empty($action->packet_dump))
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title"><strong>Packet Dump</strong></h3></div>
                <div class="panel-body">
                    <pre>
@foreach(json_decode($action->packet_dump, true) as $packet){{$packet}}
@endforeach
                    </pre>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>




<!-- MODALS -->
@if(!is_null($action->raw))
<div class="modal fade raw_json_modal" tabindex="-1" role="dialog" aria-labelledby="raw_json_modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Raw JSON</h4>
        </div>
        <div class="model-body">
            <pre>{!! $action->rawPretty()  !!}</pre>
        </div>
    </div>
  </div>
</div>
@endif

@endsection
