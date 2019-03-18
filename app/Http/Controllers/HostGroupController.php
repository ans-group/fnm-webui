<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DC;
use App\HostGroup;

class HostGroupController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('admin', ['except' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index() {
         if(isset($_GET['dc']) && is_numeric($_GET['dc'])) {
             $filtered = true;
             $dc = DC::findOrFail($_GET['dc']);
             $hostgroups = HostGroup::with('dc')->whereHas('dc', function($q) use ($dc) { $q->where('id', $dc->id); })->get();
         } else {
             $filtered = false;
             $hostgroups = HostGroup::all();
         }
         return view('hostgroup.index')->with('hgs', $hostgroups)->with('filtered', $filtered);
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\App\DC::all()->count() === 0) {
            return redirect()->route('dc.create')->withErrors(["Cannot create Host Group until a DC has been created."]);
        }
        return view('hostgroup.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:host_groups,name|alpha_dash|min:3|max:200',
            'description' => 'sometimes|nullable',
            'dc_id' => 'required|numeric|exists:dc,id',
        ]);
        $dc = DC::findOrFail($validatedData['dc_id']);
        $validatedData['full_name'] = strtolower($dc->name) . "_" . $validatedData['name'];

        $response = $dc->manageHostGroup($validatedData['full_name'], "PUT");
        if (!$response['success']) {
            return back()->withErrors($response['error_text'])->withInput($request->input());
        }

        $new = HostGroup::Create($validatedData);
        $new->setDescription($validatedData['description']);
        return redirect()->route('hostgroup.edit', $new)->withSuccess("Host group has been created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(HostGroup $hostgroup)
    {
        $ips = $hostgroup->ips()->paginate(20);
        return view('hostgroup.show')->with('hg', $hostgroup)->with('ips', $ips);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(HostGroup $hostgroup)
    {
        return view('hostgroup.edit')->with('hg', $hostgroup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HostGroup $hostgroup)
    {
        // Validate the input
        $validatedData = $request->validate([
            'description' => 'sometimes|nullable',
        ]);

        $hostgroup->update($validatedData);
        $hostgroup->setDescription($validatedData['description']);
        return redirect()->route('hostgroup.edit', $hostgroup)->withSuccess("Host Group has been updated.");
    }

    public function updateThresholds(Request $request, HostGroup $hostgroup)
    {
        // Validate the input
        $validatedData = $request->validate([
            'enable_ban' => 'required|boolean',
            'threshold_pps' => 'required|numeric|min:0',
            'threshold_mbps' => 'required|numeric|min:0',
            'threshold_flows' => 'required|numeric|min:0',
        ]);

        if($validatedData['enable_ban'] == true) {
            $validatedData['enable_ban'] = "enable";
        } else {
            $validatedData['enable_ban'] = "disable";
        }

        // Handle all the aliases that are also in the API...
        $validatedData['ban_for_pps'] = $validatedData['enable_ban'];
        $validatedData['ban_for_tcp_pps'] = $validatedData['enable_ban'];
        $validatedData['ban_for_udp_pps'] = $validatedData['enable_ban'];
        $validatedData['ban_for_icmp_pps'] = $validatedData['enable_ban'];
        $validatedData['ban_for_bandwidth'] = $validatedData['enable_ban'];
        $validatedData['ban_for_tcp_bandwidth'] = $validatedData['enable_ban'];
        $validatedData['ban_for_udp_bandwidth'] = $validatedData['enable_ban'];
        $validatedData['ban_for_icmp_bandwidth'] = $validatedData['enable_ban'];
        $validatedData['ban_for_flows'] = $validatedData['enable_ban'];

        $validatedData['threshold_tcp_pps'] = $validatedData['threshold_pps'];
        $validatedData['threshold_udp_pps'] = $validatedData['threshold_pps'];
        $validatedData['threshold_icmp_pps'] = $validatedData['threshold_pps'];

        $validatedData['threshold_tcp_mbps'] = $validatedData['threshold_mbps'];
        $validatedData['threshold_udp_mbps'] = $validatedData['threshold_mbps'];
        $validatedData['threshold_icmp_mbps'] = $validatedData['threshold_mbps'];

        $response = $hostgroup->setThresholds($validatedData);

        if($response->where('success', false)->count() > 0) {
            $errors = $response->where('success', false)->pluck('error_text_full');
            return back()->withInput()->withErrors($errors);
        }

        return redirect()->route('hostgroup.edit', $hostgroup)->withSuccess("Host Group thresholds updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(HostGroup $hostgroup)
    {
        $name = $hostgroup->fullname();
        $hostgroup->remove();
        return redirect()->route('hostgroup.index')->withSuccess("Host group $name has been deleted.");
    }
}
