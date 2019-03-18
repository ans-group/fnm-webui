@extends('layouts.app')

@section('title')
Edit Host Group
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Edit Host Group <small>&ndash; {{ strtolower($hg->dc->name) }}_{{ $hg->name }}</h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('hostgroup.index') }}" class="btn btn-link">&larr; All Host Groups</a>&nbsp;
            <a href="{{ route('hostgroup.show', $hg) }}" class="btn btn-default"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; View</a>&nbsp;
            @if(Auth::user()->admin)
            <a href="{{ route('hostgroup.delete', $hg) }}" class="btn btn-primary loading" onclick="return confirm('*** Are you sure you want to delete this Host Group? ***\n\nThis is a cascading action and will also delete the IP ranges owned by this Host Group.\n\nOnce deleted, those IPs will no longer be monitored by FNM, and therefore won\'t be banned if attacked.');"><i class="fa fa-trash" aria-hidden="true"></i> &nbsp; <strong>Delete</strong></a>
            @endif
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Error processing input:</h4>
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

        <form method="POST" action="{{ route('hostgroup.update', $hg) }}">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Group Configuration</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group{{ $errors->has('dc_id') ? ' has-error' : '' }}">
                            <label for="dc_id" class="control-label">FastNetMon Instance: <small class="text-muted"> &nbsp; (cannot be changed)</small></label>
                            <select name="dc_id" id="dc_id" class="form-control" onchange="return changeDC();" disabled>
                                @foreach(App\DC::where('active', 1)->get() as $dc)
                                <option value="{{ $dc->id }}" {{ old('dc_id', $hg->dc->id)==$dc->id ? ' selected' : '' }}>{{ $dc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="control-label">Group Name: <small class="text-muted"> &nbsp; (must be unique, cannot be changed)</small></label>
                            <div class="input-group">
                                <div class="input-group-addon" id="dc_name">{{ $hg->dc->name }}_</div>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $hg->name) }}" placeholder="Group name (eg: main_network)" required disabled>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="control-label">Group Description:</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Detailed description of this group">{{ old('description', $hg->description) }}</textarea>
                        </div>
                        <br>
                        <button class="btn btn-info btn-block loading" type="submit"><i class="fa fa-pencil"></i> &nbsp; <strong>Update Host Group</strong></button>
                    </div>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('hostgroup.thresholds', $hg) }}">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Threshold Configuration</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group{{ $errors->has('enable_ban') ? ' has-error' : '' }}">
                            <label for="enable_ban" class="control-label">Bans Enabled:</label>
                            <select name="enable_ban" class="form-control">
                                <option value="1" {{ old('enable_ban', $hg->meta()['enable_ban']) == true ? ' selected' : '' }}>Yes - IPs will be banned if over threshold</option>
                                <option value="0" {{ old('enable_ban', $hg->meta()['enable_ban']) == false ? ' selected' : '' }}>No - The thresholds will not apply</option>
                            </select>
                        </div>
                        <br>
                        <h4>Thresholds &nbsp; <small>(TCP + UDP + ICMP, both directions)</small></h4>
                        <div class="form-group{{ $errors->has('threshold_pps') ? ' has-error' : '' }}">
                            <label for="threshold_pps" class="control-label">Packets per second:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="threshold_pps" value="{{ old('threshold_pps', $hg->meta()['threshold_pps']) }}" placeholder="1000" required>
                                <div class="input-group-addon">pps</div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('threshold_mbps') ? ' has-error' : '' }}">
                            <label for="threshold_mbps" class="control-label">Bandwidth:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="threshold_mbps" value="{{ old('threshold_mbps', $hg->meta()['threshold_mbps']) }}" placeholder="1000" required>
                                <div class="input-group-addon">mbps</div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('threshold_flows') ? ' has-error' : '' }}">
                            <label for="threshold_flows" class="control-label">Flows:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="threshold_flows" value="{{ old('threshold_flows', $hg->meta()['threshold_flows']) }}" placeholder="1000" required>
                                <div class="input-group-addon">flows</div>
                            </div>
                        </div>


                        <br>
                        <button class="btn btn-warning btn-block loading" type="submit"><i class="fa fa-wrench"></i> &nbsp; <strong>Update Thresholds</strong></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
