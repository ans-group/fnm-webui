<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DC;
use App\HostGroup;
use App\IP;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $dcs = DC::where('active', 1)->get();
        $actions = \App\Actions::all()->sortByDesc('id')->take(10);
        $blackholes = [];
        foreach($dcs as $dc) {
            $bl = $dc->getBlackholes();
            if($bl == false) { break; }
            foreach($bl as $key=>$value) { $bl[$key]['dc_id'] = $dc->id; $bl[$key]['dc_name'] = $dc->name; }
            $blackholes = array_merge($blackholes, $bl);
        }
        return view('home')->with('hostTraffic', $this->getHostTraffic())->with('dcs', $dcs)->with('blackholes', $blackholes)->with('actions', $actions);
    }

    public function getHostTraffic() {
        // Prepare for the traffic list
        $dcHostTraffic = [];
        $dcs = \App\DC::where('active', 1)->get();

        // Build an array of all traffic
        foreach ($dcs as $dc) {
            $traf = $dc->hostTraffic();
            if($traf == false) { break; }
            $dcHostTraffic = array_merge( $dcHostTraffic, $traf );
        }

        // Sort that list!
        $hostTraffic = collect($dcHostTraffic);
        $sortedHostTraffic = $hostTraffic->sortByDesc(function($host, $key){
            return $host['incoming_packets'];
        });

        return $sortedHostTraffic;
    }

    public function createBlackhole (Request $request) {
        // Validate the input
        $validatedData = $request->validate([
            'ip' => ['required', 'regex:/^([0-9]{1,3}\.){3}[0-9]{1,3}?$/i']
        ]);

        $long = ip2long($validatedData['ip']);
        $ip = $validatedData['ip']."/32";
        $range = IP::where('start_ip', '<=', $long)->where('end_ip', '>=', $long)->orderBy('cidr', 'desc')->first();

        if(is_null($range)) {
            return redirect()->back()->withErrors(["Cannot find configured IP range for: ". $validatedData['ip']]);
        }

        $alreadyBanned = $range->dc->getBlackholeUUID($validatedData['ip']);

        if(!is_null($alreadyBanned)) {
            return redirect()->back()->withErrors([$validatedData['ip']." is already banned with UUID: ".$alreadyBanned]);
        }

        $block = $range->dc->manageBlackhole($validatedData['ip'], "PUT");
        if(!$block['success']) {
            return redirect()->back()->withErrors( [$range->dc->name, $block['error_text'] ] );
        }

        return redirect()->back()->withSuccess("IP address blackholed: ". $validatedData['ip']);
    }

    public function deleteBlackhole (Request $request) {
        // Validate the input
        $validatedData = $request->validate([
            'ip' => ['required', 'regex:/^([0-9]{1,3}\.){3}[0-9]{1,3}?$/i']
        ]);

        $long = ip2long($validatedData['ip']);
        $range = IP::where('start_ip', '<=', $long)->where('end_ip', '>=', $long)->orderBy('cidr', 'desc')->first();

        if(is_null($range)) {
            return redirect()->back()->withErrors(["Cannot find configured IP range for: ". $validatedData['ip']]);
        }

        $uuid = $range->dc->getBlackholeUUID($validatedData['ip']);

        if(is_null($uuid)) {
            return redirect()->back()->withErrors([$validatedData['ip']." is not currently banned."]);
        }

        $block = $range->dc->manageBlackhole($uuid, "DELETE");
        if(!$block['success']) {
            return redirect()->back()->withErrors( [$range->dc->name, $block['error_text'] ] );
        }

        return redirect()->back()->withSuccess("IP address blackhole removed: ". $validatedData['ip']);
    }
}
