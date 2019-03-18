@extends('layouts.app')

@section('title')
Edit IP Range
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Edit IP range <small>&ndash; {{ $ip->range }}</small></h2>
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

        <form method="POST" action="{{ route('ip.update', $ip) }}">
            @csrf
            @method('PUT')

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Range Configuration</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group{{ $errors->has('hostgroup_id') ? ' has-error' : '' }}">
                            <label for="hostgroup_id" class="control-label">Host Group:</label>
                            <input type="text" name="hostgroup_id" id="hostgroup_id" class="form-control" value="{{ $ip->hostgroup->fullname() }}" disabled>
                        </div>
                        <div class="form-group{{ $errors->has('version') ? ' has-error' : '' }}">
                            <label for="version" class="control-label">IP Version:</label>
                            <input type="text" name="version" id="version" class="form-control" value="IPv{{ $ip->version }}" disabled>
                        </div>
                        <div class="form-group{{ $errors->has('range') ? ' has-error' : '' }}">
                            <label for="range" class="control-label">IP Range: <small class="text-muted"> &nbsp; (must be in CIDR notation format)</small></label>
                            <input type="text" class="form-control" name="range" value="{{ $ip->range }}" disabled>
                        </div>
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="control-label">Description:</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Notes about this IP range">{{ old('description', $ip->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <button class="btn btn-info loading" type="submit">Update IP Range</button>
            </div>
            <div class="col-md-6 text-right">
                <form action="{{ route('ip.destroy', $ip) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-primary loading" type="submit"><i class="fa fa-trash"></i> &nbsp;Delete IP Range</button>
                </form>
            </div>

        </form>

    </div>
</div>
@endsection
