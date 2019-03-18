@if ($dc->license()['licensed_bandwidth'] === 0)
 &nbsp; <i class="fa fa-exclamation-triangle text-warning" data-toggle="tooltip" data-placement="right" title="This instance is not licensed"></i>
@elseif ($dc->license()['expiration_date'] > Carbon::now())
&nbsp; <i class="fa fa-exclamation-triangle text-warning" data-toggle="tooltip" data-placement="right" title="This instance has an expired license"></i>
@endif
$dc->license()['expiration_date'] 
