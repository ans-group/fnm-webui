<html>
<head>
<style>
html,body{
    font-family: 'Arial', 'Tahoma', sans-serif !important;
}
td {
    padding: 5px;
}
table {
    border: 4px solid black;
}
</style>
</head>

<body style="font-family: Verdana, sans-serif;">
<h2>FastNetMon Action Received</h2>

<p>
    <ul>
        <li><a href="{{ route('action.show', $action) }}">Click to view details in dashboard &rarr;</a><br><br></li>
    </ul>
</p>

<table>
    <tr>
        <td colspan="2"><center>Host Details</center></td>
    </tr>
    <tr>
        <td>IP:</td>
        <td>{{ $action->ip }}</td>
    </tr>
    <tr>
        <td>DC:</td>
        <td><a href="{{ route('dc.show', $action->dc) }}">{{ $action->dc->name }}</a></td>
    </tr>
    <tr>
        <td>Host Group:</td>
        <td><a href="{{ route('hostgroup.show', $action->hostgroup) }}">{{ $action->hostgroup->fullname() }}</a></td>
    </tr>
    <tr>
        <td>Action:</td>
        <td style="background-color: {{ $action->action == 'ban' ? 'red' : 'green' }}; color: white;"><strong>{{ strtoupper($action->attack_detection_source." ".$action->action) }}</strong></td>
    </tr>
    <tr>
        <td>UUID:</td>
        <td><code>{{ $action->uuid }}</code></td>
    </tr>
    <tr>
        <td>Timestamp:</td>
        <td>{{ $action->created_at }}</td>
    </tr>
</table>

<br>

<table width="500px">
    <tr>
        <td colspan="2"><center>Attack Details</center></td>
    </tr>
    <tr>
        <td>Attack Severity:</td>
        <td>{{ $action->attack_severity }}</td>
    </tr>
    <tr>
        <td>Attack Direction:</td>
        <td>{{ $action->attack_direction }}</td>
    </tr>
    <tr>
        <td>Attack Protocol:</td>
        <td>{{ $action->attack_protocol }}</td>
    </tr>
    <tr>
        <td>Attack Type:</td>
        <td>{{ $action->attack_type }}</td>
    </tr>
    <tr>
        <td>Initial Attack Power:</td>
        <td>{{ $action->attack_initial_power }} pps</td>
    </tr>
    <tr>
        <td>Initial Peak Power:</td>
        <td>{{ $action->attack_peak_power }} pps</td>
    </tr>
    <tr>
        <td>Incoming Traffic:</td>
        <td>{{ round($action->attack_total_incoming_traffic /1024) }} mbps</td>
    </tr>
    <tr>
        <td>Outgoing Traffic:</td>
        <td>{{ round($action->attack_total_outgoing_traffic /1024) }} mbps</td>
    </tr>
</table>

</body>
