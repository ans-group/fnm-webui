@extends('layouts.app')

@section('title')
Create IP Range
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Create a new IP range <small>&ndash; Configure new range</small></h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('ip.index') }}" class="btn btn-default"><i class="fa fa-cogs" aria-hidden="true"></i> &nbsp; Manage IPs</a>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Error validating input:</h4>
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

        <form method="POST" action="{{ route('ip.store') }}">
            @csrf

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Range Configuration</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group{{ $errors->has('hostgroup_id') ? ' has-error' : '' }}">
                            <label for="hostgroup_id" class="control-label">Host Group:</label>
                            <select name="hostgroup_id" id="hostgroup_id" class="form-control">
                                @foreach($dc->hostgroups as $hg)
                                <option value="{{ $hg->id }}" {{ old('hostgroup_id')==$hg->id ? ' selected' : '' }}>{{ $hg->fullname() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group{{ $errors->has('version') ? ' has-error' : '' }}">
                            <label for="version" class="control-label">IP Version:</label>
                            <select name="version" id="version" class="form-control">
                                <option value="4" {{ old('version')==4 ? ' selected' : '' }}>IPv4</option>
                                <option value="6" {{ old('version')==6 ? ' selected' : '' }} disabled>IPv6 (coming soon)</option>
                            </select>
                        </div>
                        <div class="form-group{{ $errors->has('range') ? ' has-error' : '' }}">
                            <label for="range" class="control-label">IP Range: <small class="text-muted"> &nbsp; (must be in CIDR notation format, one per line)</small></label>
                            <textarea class="form-control" name="range" rows="5" placeholder="10.0.0.0/16
192.168.0.0/24" required>{{ old('range') }}</textarea>
                        </div>
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="control-label">Description:</label>
                            <input type="text" name="description" class="form-control" rows="5" placeholder="Notes about this IP range" value="{{ old('description') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <button class="btn btn-info loading" type="submit">Create IP Range</button>
            </div>

        </form>

    </div>
</div>
@endsection
