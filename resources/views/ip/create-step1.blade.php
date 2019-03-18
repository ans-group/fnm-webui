@extends('layouts.app')

@section('title')
Create IP Range
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-6">
            <h2 class="title">Create a new IP range <small>&ndash; Select DC for new range</small></h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('ip.index') }}" class="btn btn-default"><i class="fa fa-cogs" aria-hidden="true"></i> &nbsp; Manage IPs</a>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Select DC</h4>
                </div>
                <div class="panel-body">
                    <br>
                    @foreach(App\DC::where('active', 1)->get() as $dc)
                        <div class="col-md-2">
                            <a href="?dc={{ $dc->id }}" data-toggle="tooltip" data-placement="top" title="{{ $dc->name }}">
                                <div class="well text-center dc-select-box">
                                    <strong class="lead">{{ $dc->name }}</strong>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
