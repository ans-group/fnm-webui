@extends('layouts.app')

@section('title')
User Management
@endsection

@section('content')
<div class="container">
    <br class="clear" />

    <div class="row">
        <div class="col-md-12">
            <h2 class="title">Changelogs</h2>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <h4>Graceful FNM instance failure &nbsp; <small>&ndash; 18th March 2019</small></h4>
            <p>Previously, an Error 500 would show when there were connection errors to a FNM instance (DC). This will now show an error banner and zero-value data.</p>
            <br>
        </div>

        <div class="col-md-12">
            <h4>UI updates &nbsp; <small>&ndash; 27th February 2019</small></h4>
            <p>This is the initial live version of the FNM UI, with the following functionality.</p>
            <p>
                <ul>
                    <li>Log the last login time and IP for each user</li>
                    <li>[Admin] - Added the last_login_time and last_login_ip to the user list</li>
                    <li>Bugfixes for action linking from hostgroups / DCs</li>
                </ul>
            </p>
            <br>
        </div>

        <div class="col-md-12">
            <h4>Initial release &nbsp; <small>&ndash; January 2019</small></h4>
            <p>This is the initial live version of the FNM UI, with the following functionality.</p>
            <p>
                <ul>
                    <li>User management with basic permissions (user or admin)</li>
                    <li>DC management with validation to confirm connevtivity to the FNM instance</li>
                    <li>Hostgroup creation, modification, and deletion</li>
                    <li>IP range creation, modification, and deletion</li>
                    <li>Handling of inbound webhooks from FNM servers</li>
                </ul>
            </p>
        </div>
    </div>
</div>
@endsection
