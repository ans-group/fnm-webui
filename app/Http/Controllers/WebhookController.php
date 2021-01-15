<?php

namespace App\Http\Controllers;

use App\IP;
use App\DC;
use App\User;
use App\Actions;
use App\Mail\ActionReceived;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Client;

class WebhookController extends Controller
{
    public function __construct() {
        //$this->middleware('guest');
    }

    public function handle(Request $request) {
        $dc = DC::where('allowed_ip', $request->ip())->first();

        if($dc->count() < 1) {
            abort(401);
        }

        // Validate the UA
        if($request->header('User-Agent') !== "FastNetMon") {
            return abort(403);
        }

        // Check we received data
        if(is_null($request->getContent()) || empty($request->getContent())) {
            return abort(400);
        }

        // Try to unmarshall
        try {
            $data = json_decode($request->getContent(), true);
        } catch(\Exception $e) {
            return abort(501);
        }

        // Get some models...
        $ip = new IP;
        $action = new Actions;

        // Try to find the IP range for this host
        $range = $ip->find($data['ip'], $dc->id);
        if($range === false or is_null($range)) {
            return abort(404, "Cannot find IP range");
        }

        $action->raw = $request->getContent();
        $action->ip = $data['ip'];
        $action->action = $data['action'];
        $action->uuid = $data['attack_details']['attack_uuid'];
        $action->ip_id = $range->id;
        $action->hostgroup_id = $range->hostgroup->id;
        $action->dc_id = $range->hostgroup->dc->id;

        $action->attack_severity                   = $data['attack_details']['attack_severity'];
        $action->attack_direction                  = $data['attack_details']['attack_direction'];
        $action->attack_type                       = $data['attack_details']['attack_type'];
        $action->attack_protocol                   = $data['attack_details']['attack_protocol'];
        $action->attack_detection_source           = $data['attack_details']['attack_detection_source'];
        $action->attack_initial_power              = $data['attack_details']['initial_attack_power'];
        $action->attack_peak_power                 = $data['attack_details']['peak_attack_power'];
        $action->attack_total_incoming_traffic     = $data['attack_details']['total_incoming_traffic'];
        $action->attack_total_outgoing_traffic     = $data['attack_details']['total_outgoing_traffic'];
        $action->attack_total_incoming_pps         = $data['attack_details']['total_incoming_pps'];
        $action->attack_total_outgoing_pps         = $data['attack_details']['total_outgoing_pps'];
        $action->attack_total_incoming_flows       = $data['attack_details']['total_incoming_flows'];
        $action->attack_total_outgoing_flows       = $data['attack_details']['total_outgoing_flows'];

        if(isset($data['packet_dump']) && !empty($data['packet_dump'])) {
            $action->packet_dump = json_encode($data['packet_dump']);
        }

        $action->save();

        $to = User::where(['active' => true, 'notify' => true])->pluck('email');
        $cc = env('ACTION_CC', false);
        if (env('FORWARD_WEBHOOK')) {
            try {
                $client = new Client();
                $client->post(env('FORWARD_WEBHOOK'), [
                    'json' => [
                        'ui' => [
                            'dc_id' => $dc->id,
                            'dc_name' => $dc->name,
                            'action_id' => $action->id,
                            'created_at' => \Carbon\Carbon::now()->format(\DateTime::ATOM),
                            'hostgroup_id' => $action->hostgroup_id,
                            'hostgroup' => $action->hostgroup->name,
                            'email_to' => $to,
                            'email_cc' => explode(",", env('ACTION_CC', null)),
                        ],
                        'fastnet' => $request->all(),
                    ],
                    'headers' => ['Authorization' => env('FORWARD_WEBHOOK_AUTH', '')],
                ]);
            } catch (\Exception $e) {
                \Log::critical("Failed to send webhook", [$e]);
            } finally {}
        }

        if($cc !== false) {
            $cc = explode(",",$cc);
            Mail::to($to)->cc($cc)->send(new ActionReceived($action));
        } else {
            Mail::to($to)->send(new ActionReceived($action));
        }
        Cache::forget('dc:'.$action->dc_id.':blackhole');
    }

}
