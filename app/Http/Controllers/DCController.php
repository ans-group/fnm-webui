<?php

namespace App\Http\Controllers;

use App\DC;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Client;

class DCController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('admin', ['except' => ['index','show']]);
    }

    // View all DCs
    public function index() {
        $dcs = \App\DC::all();
        return view('dc.index')->with('dcs', $dcs);
    }

    public function show(DC $dc) {
        return view('dc.show')->with('dc', $dc);
    }

    // Form to create a new DC
    public function create() {
        return view('dc.create');
    }

    // Logic for creating a new DC
    public function store(Request $request) {

        // Validate the input
        $validatedData = $request->validate([
            'name' => 'required|unique:dc|max:255',
            'active' => 'required|boolean',
            'description' => 'sometimes',
            'api_url' => 'required|regex:/^http(s?):\/\/.+\/$/i',
            'api_username' => 'required|min:3',
            'api_password' => 'required|confirmed|min:6',
            'allowed_ip' => ['required', 'regex:/^([0-9]{1,3}\.){3}[0-9]{1,3}?$/i'],
        ]);

        // Test that we can connect to the API
        try {
            $client = new Client();
            $response = $client->request('GET', $validatedData['api_url'] . "license",
                [ 'auth' => [ $validatedData['api_username'], $validatedData['api_password'] ] ]
            );
        } catch (\Exception $e) {
            // There was an error connecting to the FNM API
            return back()->withErrors($e->getMessage())->withInput($request->input());
        }

        // Confirm the FNM API gate the correct response
        $res = json_decode($response->getBody());
        if(!$res->success) {
            return back()->withErrors("API gave invalid JSON response.")->withInput($request->input());
        }

        // Create a new DC in the DB and redirect to it's page
        $validatedData['api_password'] = encrypt($validatedData['api_password']);
        $new = DC::Create($validatedData);
        return redirect()->route('dc.show', [$new])->withSuccess("DC has been created.");
    }

    public function edit(DC $dc) {
        return view('dc.edit')->with('dc', $dc);
    }

    // Logic for creating a new DC
    public function update(DC $dc, Request $request) {

        // Validate the input
        $validatedData = $request->validate([
            'name' => 'required|alpha_dash|unique:dc,id,'.$dc->id.'|max:255',
            'active' => 'required|boolean',
            'description' => 'sometimes|nullable',
            'api_url' => 'required|regex:/^http(s?):\/\/.+\/$/i',
            'api_username' => 'required|min:3',
            'api_password' => 'sometimes|confirmed',
            'allowed_ip' => ['required', 'regex:/^([0-9]{1,3}\.){3}[0-9]{1,3}?$/i'],
        ]);

        // Test that we can connect to the API
        if( isset($validatedData['api_password']) && !empty($validatedData['api_password']) ) {
            try {
                $client = new Client();
                $response = $client->request('GET', $validatedData['api_url'] . "license",
                    [ 'auth' => [ $validatedData['api_username'], $validatedData['api_password'] ] ]
                );
            } catch (\Exception $e) {
                // There was an error connecting to the FNM API
                return back()->withErrors($e->getMessage())->withInput($request->input());
            }

            // Confirm the FNM API gate the correct response
            $res = json_decode($response->getBody());
            if(!$res->success) {
                return back()->withErrors("API gave invalid JSON response.")->withInput($request->input());
            }

            $validatedData['api_password'] = encrypt($validatedData['api_password']);
        } else {
            unset($validatedData['api_password']);
        }

        // Create a new DC in the DB and redirect to it's page
        $dc->update($validatedData);
        return redirect()->route('dc.edit', [$dc])->withSuccess("DC has been updated.");
    }

    public function destroy(DC $dc) {
        $name = $dc->name;
        $dc->remove();
        return redirect()->route('dc.index')->withSuccess("DC $name has been deleted.");
    }

    public function toggleBan (DC $dc) {
        $current = $dc->banStatus();

        if($current) {
            $dc->banStatus("disable");
        } else {
            $dc->banStatus("enable");
        }

        $state = !$current ? 'enabled' : 'disabled';

        return back()->with('success', 'DC automatic banning '.$state.'.');
    }

    public function toggleUnban (DC $dc) {
        $current = $dc->unbanStatus();

        if($current) {
            $dc->unbanStatus("disable");
        } else {
            $dc->unbanStatus("enable");
        }

        $state = !$current ? 'enabled' : 'disabled';

        return back()->with('success', 'DC automatic unbanning '.$state.'.');
    }


}
