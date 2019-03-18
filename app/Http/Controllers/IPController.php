<?php

namespace App\Http\Controllers;

use App\IP;
use App\DC;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Client;

class IPController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('admin', ['except' => ['index','show']]);
    }

    // View all ips
    public function index() {
        if(isset($_GET['dc']) && is_numeric($_GET['dc'])) {
            $filtered = true;
            $dc = DC::findOrFail($_GET['dc']);
            $ips = IP::with('hostgroup')->whereHas('hostgroup', function($q) use ($dc) { $q->where('dc_id', $dc->id); })->paginate(20);
        } else {
            $filtered = false;
            $ips = IP::paginate(20);
        }
        return view('ip.index')->with('ips', $ips)->with('filtered', $filtered);
    }

    # TODO: Add a page for per-range stats
    public function show(ip $ip) {
        return redirect()->route('ip.edit', $ip);
        //return view('ip.show')->with('ip', $ip);
    }

    // Form to create a new ip
    public function create() {
        if(\App\DC::all()->count() === 0) {
            return redirect()->route('dc.create')->withErrors(["Cannot create an IP range until a DC has been created."]);
        } elseif(\App\HostGroup::all()->count() === 0) {
            return redirect()->route('hostgroup.create')->withErrors(["Cannot create an IP range until a Host Group has been created."]);
        }

        // No DC was selected
        if(!isset($_GET['dc']) || empty($_GET['dc']) || !is_numeric($_GET['dc']) || $_GET['dc'] == 0) {
            return view('ip.create-step1');
        }

        // DC was specified, send to the create form
        $dc = \App\DC::findOrFail($_GET['dc']);
        if($dc->hostgroups->count() === 0) {
            return redirect()->route('hostgroup.create')->withErrors(["Cannot create an IP range until a Host Group has been created in ".$dc->name."."]);
        }
        return view('ip.create-step2')->with('dc', $dc);
    }

    // Logic for creating a new ip
    public function store(Request $request) {
        // Validate the input
        $validatedData = $request->validate([
            'range' => ['required', 'unique:ip', 'regex:/^(([0-9]{1,3}\.){3}[0-9]{1,3}(\/([0-9]|[1-2][0-9]|3[0-2]))([\r\n])*)+$/i'],
            'description' => 'sometimes',
            'hostgroup_id' => 'required|numeric|exists:host_groups,id',
        ]);

        $hg = \App\HostGroup::findOrFail($validatedData['hostgroup_id']);

        // Handle multiple IP input
        $splitIPs = preg_split('/[\n\r]+/', $validatedData['range']);

        foreach($splitIPs as $i) {
            // Add the IP to FNM network_list
            try {
                $addToDC = $hg->dc->manageIP($i, "PUT", false); // Don't commit until the end of the loop.
            } catch(\Exception $e) {
                return back()->withErrors("API Error: ".$e->getMessage())->withInput($request->input());
            }

            if($addToDC['success'] == false) {
                return back()->withErrors("Error adding IP to network_list: ". $addToDC['error_text'])->withInput($request->input());
            }
            // Add the IP to the HostGroup in FNM
            try{
                $addToHG = $hg->manageIP($i, "PUT", false); // Don't commit until the end of the loop.
            } catch(\Exception $e) {
                return back()->withErrors("API Error: ".$e->getMessage())->withInput($request->input());
            }

            if($addToHG['success'] == false) {
                return back()->withErrors("Error adding IP to Host Group: ". $addToHG['error_text'])->withInput($request->input());
            }

            // Clone the validated data for a single IP instead of a list
            $singleIP = $validatedData;
            $singleIP['range'] = $i;

            // Create a new ip in the DB and redirect to it's page
            $ip = new IP;
            $cidr = $ip->explodeCIDR($i);
            $singleIP['start_ip'] = ip2long($cidr[0]);
            $singleIP['end_ip'] = ip2long($cidr[1]);
            $singleIP['cidr'] = $cidr[2];
            $ip->update($singleIP);

            $new = IP::Create($singleIP);
        }

        // Manual commit to avoid breaking loop
        $hg->dc->commit();

        $ranges = $validatedData['range'];
        return redirect()->route('ip.index')->withSuccess("IP range(s) have been created: $ranges");
    }

    public function edit(ip $ip) {
        return view('ip.edit')->with('ip', $ip);
    }

    // Logic for creating a new ip
    public function update(ip $ip, Request $request) {

        // Validate the input
        $validatedData = $request->validate([
            'description' => 'sometimes',
        ]);

        $ip->update($validatedData);

        return redirect()->route('ip.edit', [$ip])->withSuccess("Range has been updated.");
    }

    public function destroy(ip $ip) {
        $name = $ip->range;
        $ip->remove();
        return redirect()->route('ip.index')->withSuccess("Range $name has been deleted.");
    }

    public function findRange() {
        if(!isset($_GET['q']) || is_null($_GET['q']) || empty($_GET['q'])) {
            return abort(400, "No query specified");
        }

        $ip = urldecode($_GET['q']);

        $api = new IP;
        $find = $api->find($ip);

        // Poor man's validation
        if($find === false) {
            return abort(400, "Query is not an Ipv4 or CIDR");
        }

        if(is_null($find)) {
            return abort(404, "Range cannot be found");
        }

        return redirect()->route('ip.edit', $find);
    }
}
