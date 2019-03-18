@extends('layouts.app')

@section('title')
Create Host Group
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Create a new Host Group</h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('hostgroup.index') }}" class="btn btn-default"><i class="fa fa-cogs" aria-hidden="true"></i> &nbsp; Manage Host Groups</a>
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

        <form method="POST" action="{{ route('hostgroup.store') }}">
            @csrf

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Group Configuration</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group{{ $errors->has('dc_id') ? ' has-error' : '' }}">
                            <label for="dc_id" class="control-label">FastNetMon Instance:</label>
                            <select name="dc_id" id="dc_id" class="form-control" onchange="return changeDC();">
                                @foreach(App\DC::where('active', 1)->get() as $dc)
                                <option value="{{ $dc->id }}" {{ old('dc_id')==$dc->id ? ' selected' : '' }}>{{ $dc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="control-label">Group Name: <small class="text-muted"> &nbsp; (must be unique, no spaces, will have the DC name preprended)</small></label>
                            <div class="input-group">
                                <div class="input-group-addon" id="dc_name">{{ old('dc_id', 'dc_') }}</div>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Group name (eg: main_network)" required>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="control-label">Group Description:</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Detailed description of this group">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <button class="btn btn-info loading" type="submit">Create Host Group</button>
            </div>

        </form>

    </div>
</div>
@endsection

@section('js')
<script>
function changeDC() {
    selected = $("#dc_id option:selected").text().toLowerCase();
    $('#dc_name').text(selected + "_");
}

$(document).ready(function() {
    changeDC();
});
</script>
@endsection
