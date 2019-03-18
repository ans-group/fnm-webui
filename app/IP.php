<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IP extends Model
{
    protected $table = "ip";

    protected $fillable = [
        'range', 'description', 'hostgroup_id', 'start_ip', 'end_ip', 'cidr',
    ];

    public function hostgroup() {
        return $this->belongsTo("App\HostGroup");
    }

    public function dc() {
        return $this->hostgroup->dc();
    }

    public function remove() {
        $this->dc->manageIP($this->range, "DELETE"); // Delete from FNM networks_list
        $this->hostgroup->manageIP($this->range, "DELETE"); // Delete from FNM HG networks
        $this->delete();
    }

    // This is magic
    public function explodeCIDR($cidr) {
        $range = array();
        $cidr = explode('/', $cidr);
        $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
        $range[1] = long2ip((ip2long($range[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
        $range[2] = $cidr[1];
        return $range;
    }

    public function find($ip, $dc_id = false) {
        // Poor man's validation
        if(!preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}(\/([0-9]|[1-2][0-9]|3[0-2]))?$/i', $ip)) {
            return false;
        }

        if(!is_numeric($dc_id)) {
            $dc_id = false;
        }

        if(strpos($ip, '/') !== false) {
            // Handle CIDR
            $arr = explode('/', $ip);
            $long = ip2long($arr[0]);
        } else {
            // Handle /32
            $long = ip2long($ip);
        }

        if($dc_id === false ) {
            $range = IP::where('start_ip', '<=', $long)->where('end_ip', '>=', $long)->orderBy('cidr', 'desc')->first();
        } else {
            $range = IP::where('start_ip', '<=', $long)->where('end_ip', '>=', $long)->whereHas('hostgroup', function($q) use($dc_id) {
                $q->where('dc_id', $dc_id);
            })->with(['hostgroup'])->orderBy('cidr', 'desc')->first();
        }

        return $range;
    }
}
