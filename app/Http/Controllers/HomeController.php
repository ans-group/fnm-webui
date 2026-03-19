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
        
        // Find all IP ranges that contain this IP address across all DCs
        $ranges = IP::where('start_ip', '<=', $long)
                    ->where('end_ip', '>=', $long)
                    ->with(['hostgroup.dc'])
                    ->orderBy('cidr', 'desc')
                    ->get();

        if($ranges->isEmpty()) {
            return redirect()->back()->withErrors(["Cannot find configured IP range for: ". $validatedData['ip']]);
        }

        $errors = [];
        $successes = [];
        $alreadyBanned = [];

        // Process each matching range/DC
        foreach($ranges as $range) {
            $dc = $range->hostgroup->dc;
            
            // Check if already banned in this DC
            $existingBan = $dc->getBlackholeUUID($validatedData['ip']);
            if(!is_null($existingBan)) {
                $alreadyBanned[] = "{$dc->name}: already banned with UUID: {$existingBan}";
                continue;
            }

            // Attempt to ban in this DC
            $block = $dc->manageBlackhole($validatedData['ip'], "PUT");
            if(!$block['success']) {
                $errors[] = "{$dc->name}: {$block['error_text']}";
            } else {
                $successes[] = "{$dc->name}: IP blackholed successfully";
            }
        }

        // Prepare response messages
        $messages = array_merge($alreadyBanned, $successes);
        
        if(!empty($errors)) {
            if(!empty($messages)) {
                // Some succeeded, some failed
                return redirect()->back()->withErrors($errors)->with('warning', implode('; ', $messages));
            } else {
                // All failed
                return redirect()->back()->withErrors($errors);
            }
        } else {
            if(!empty($alreadyBanned) && empty($successes)) {
                // All were already banned
                return redirect()->back()->withErrors($alreadyBanned);
            } else {
                // All succeeded (some may have been already banned)
                $message = "IP address blackholed: {$validatedData['ip']}";
                if(count($messages) > 1) {
                    $message .= " (" . implode('; ', $messages) . ")";
                }
                return redirect()->back()->withSuccess($message);
            }
        }
    }

    public function deleteBlackhole (Request $request) {
        // Validate the input
        $validatedData = $request->validate([
            'ip' => ['required', 'regex:/^([0-9]{1,3}\.){3}[0-9]{1,3}?$/i']
        ]);

        $long = ip2long($validatedData['ip']);
        
        // Find all IP ranges that contain this IP address across all DCs
        $ranges = IP::where('start_ip', '<=', $long)
                    ->where('end_ip', '>=', $long)
                    ->with(['hostgroup.dc'])
                    ->orderBy('cidr', 'desc')
                    ->get();

        if($ranges->isEmpty()) {
            return redirect()->back()->withErrors(["Cannot find configured IP range for: ". $validatedData['ip']]);
        }

        $errors = [];
        $successes = [];
        $notBanned = [];

        // Process each matching range/DC
        foreach($ranges as $range) {
            $dc = $range->hostgroup->dc;
            
            // Check if banned in this DC
            $uuid = $dc->getBlackholeUUID($validatedData['ip']);
            if(is_null($uuid)) {
                $notBanned[] = "{$dc->name}: not currently banned";
                continue;
            }

            // Attempt to unban in this DC
            $block = $dc->manageBlackhole($uuid, "DELETE");
            if(!$block['success']) {
                $errors[] = "{$dc->name}: {$block['error_text']}";
            } else {
                $successes[] = "{$dc->name}: blackhole removed successfully";
            }
        }

        // Prepare response messages
        $messages = array_merge($notBanned, $successes);
        
        if(!empty($errors)) {
            if(!empty($messages)) {
                // Some succeeded, some failed
                return redirect()->back()->withErrors($errors)->with('warning', implode('; ', $messages));
            } else {
                // All failed
                return redirect()->back()->withErrors($errors);
            }
        } else {
            if(!empty($notBanned) && empty($successes)) {
                // None were banned
                return redirect()->back()->withErrors($notBanned);
            } else {
                // All succeeded (some may not have been banned)
                $message = "IP address blackhole removed: {$validatedData['ip']}";
                if(count($messages) > 1) {
                    $message .= " (" . implode('; ', $messages) . ")";
                }
                return redirect()->back()->withSuccess($message);
            }
        }
    }
}
