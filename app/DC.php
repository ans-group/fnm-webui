<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class DC extends Model
{
    protected $table = "dc";

    protected $fillable = [
        'name', 'description', 'active', 'api_url', 'api_username', 'api_password', 'allowed_ip'
    ];

    public function ip() {
        return $this->hasMany('App\IP');
    }

    public function hostgroups() {
        return $this->hasMany('App\HostGroup', 'dc_id');
    }

    public function online() {
        try {
            $this->license();
            return 1;
        } catch (\Exception $e) {
            return 0;
        }

        return 3;
    }

    public function remove() {
        // Delete host groups
        foreach($this->hostgroups as $hg) {
            $hg->remove();
        }
        $this->delete();
    }

    // Connect to a DC's API
    public function auth() {
        $creds = [ 'auth' => [], 'connect_timeout' => 0.5, 'http_errors' => false ];
        $creds['auth'][] = $this->api_username;
        $creds['auth'][] = decrypt($this->api_password);
        return $creds;
    }

    public function call($action, $method='GET') {
        try {
            $client = new Client();
            $settings = $this->auth();
            $response = $client->request($method, $this->api_url . $action, $this->auth())->getBody();
            return json_decode($response, true);
        } catch (\Exception $e) {
            Session::flash('connection-error', ['dc' => $this->name, 'error' => $e->getMessage()] );
            return ['success' => false];
        }
    }

    public function commit() {
        return $this->call('commit', 'PUT');
    }

    public function license() {
        $json = $this->call('license', 'GET');
        if(!$json['success']) {
            return false;
        }

        return $json['object'];
    }

    public function banStatus($status = null) {
        if (is_null($status)) {
            $json = $this->call('main/enable_ban', 'GET');
            return $json['success'] && $json['value'];
        } else if($status == "enable" || $status == "disable") {
            $json = $this->call("main/enable_ban/$status", 'PUT');
            $this->commit();
            return null;
        } else {
            throw new Exception("Invalid ban status");
        }
    }

    public function unbanStatus($status = null) {
        if (is_null($status)) {
            $json = $this->call("main/unban_enabled", 'GET');
            return $json['success'] && $json['value'];
        } else if($status == "enable" || $status == "disable") {
            $json = $this->call("main/unban_enabled/$status", 'PUT');
            $this->commit();
            return null;
        } else {
            throw new Exception("Invalid ban status");
        }
    }

    public function bans() {
        dd("DEPRICATED - Move to DC::getBlackholes()");
    }

    public function totalTraffic() {
        // Check if we have the totals in cache...
        $cached = Cache::get('dc:'.$this->id.':totalTraffic');
        if(!is_null($cached)) {
            return json_decode($cached, true);
        }

        // Not cached, so let's start using the API
        $json = $this->call("total_traffic_counters", 'GET');

        if(!$json['success']) {
            return false;
        }

        // Parse this data into a meaningful array:
        $totals = [];

        // Inbound MBPS
        $totals['in_mbps'] = $json['values'][1]['value'] + $json['values'][5]['value'] + $json['values'][7]['value'];
        $totals['in_mbps_suffix'] = "mbps";
        if($totals['in_mbps'] > 10240) {
            $totals['in_mbps'] = $totals['in_mbps'] / 1024;
            $totals['in_mbps_suffix'] = "gbps";
        }

        // Inbound PPS
        $totals['in_pps'] = $json['values'][0]['value'] + $json['values'][4]['value'] + $json['values'][6]['value'];
        $totals['in_pps_suffix'] = "pps";
        if($totals['in_pps'] > 10000) {
            $totals['in_pps'] = $totals['in_pps'] / 1000;
            $totals['in_pps_suffix'] = "kpps";
        }

        // Outbound MBPS
        $totals['out_mbps'] = $json['values'][3]['value'];
        $totals['out_mbps_suffix'] = "mbps";
        if($totals['out_mbps'] > 10240) {
            $totals['out_mbps'] = $totals['out_mbps'] / 1024;
            $totals['out_mbps_suffix'] = "gbps";
        }

        // Outbound PPS
        $totals['out_pps'] = $json['values'][2]['value'];
        $totals['out_pps_suffix'] = "pps";
        if($totals['out_pps'] > 10000) {
            $totals['out_pps'] = $totals['out_pps'] / 1000;
            $totals['out_pps_suffix'] = "kpps";
        }

        // Cache the totals to avoid smashing the APIs...
        Cache::put('dc:'.$this->id.':totalTraffic', json_encode($totals), 1);
        return $totals;
    }

    public function hostTraffic() {
        // Check if we have the totals in cache...
        $cached = Cache::get('dc:'.$this->id.':hostTraffic');
        if(!is_null($cached)) {
            return json_decode($cached, true);
        }

        // Not cached, so let's start using the API
        $json = $this->call("host_counters", 'GET');

        if(!$json['success']) {
            return false;
        }

        Cache::put('dc:'.$this->id.':hostTraffic', json_encode($json['values']), 1);
        return $json['values'];
    }

    public function getBlackholes($useCache = true) {
        // Check if we have the totals in cache...
        if($useCache) {
            $cached = Cache::get('dc:'.$this->id.':blackhole');
            if(!is_null($cached)) {
                return json_decode($cached, true);
            }
        }

        // Not cached, so let's start using the API
        $json = $this->call("blackhole", 'GET');

        if(!$json['success']) {
            return false;
        }

        Cache::put('dc:'.$this->id.':blackhole', json_encode($json['values']), 1);
        return $json['values'];
    }

    public function getBlackholeUUID($ip) {
        $long = ip2long($ip);
        $cidr = $ip."/32";
        $range = IP::where('start_ip', '<=', $long)->where('end_ip', '>=', $long)->orderBy('cidr', 'desc')->first();

        if(is_null($range)) {
            return false;
        }

        $blackholes = collect($range->dc->getBlackholes(false));
        $uuid = $blackholes->where('ip', $cidr)->pluck('uuid', 'ip')->first();
        return $uuid;
    }

    public function manageBlackhole($ip, $action="PUT") {
        Cache::forget('dc:'.$this->id.':blackhole');

        $ip = urlencode($ip);
        $json = $this->call("blackhole/$ip", $action);

        return $json;
    }

    public function manageHostGroup($name = null, $action = "PUT") {
        if( is_null($name) ) {
            return false;
        }

        $json = $this->call("hostgroup/$name", $action);
        $this->commit();
        return $json;
    }

    public function manageIP($ip = null, $action = "PUT", $commit = true) {
        if( is_null($ip) ) {
            return false;
        }

        $ip = urlencode($ip);
        $json = $this->call("main/networks_list/$ip", $action);
        if($commit) {
            $this->commit();
        }
        return $json;
    }
}
