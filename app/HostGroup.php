<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class HostGroup extends Model
{
    protected $table = "host_groups";

    protected $fillable = [
        'name', 'description', 'dc_id',
    ];

    public function dc() {
        return $this->belongsTo('App\DC', 'dc_id');
    }

    public function ips() {
        return $this->hasMany('App\IP', 'hostgroup_id');
    }

    public function fullname() {
        return strtolower($this->dc->name)."_".$this->name;
    }

    public function remove() {
        // Delete host groups
        foreach($this->ips as $ip) {
            $ip->remove();
        }
        $this->dc->manageHostGroup($this->fullname(), "DELETE"); // Delete from FNM
        $this->dc->commit();
        $this->delete();
    }

    public function meta() {
        $fullname = $this->fullname();
        $json = $this->dc->call("hostgroup/$fullname", 'GET');

        if(!$json['success']) {
            return false;
        }

        return $json['values'][0];
    }

    public function setDescription($desc) {
        if(is_null($desc) || empty($desc)) {
            $desc = "-";
        }

        $fullname = $this->fullname();
        $desc = urlencode($desc);
        $json = $this->dc->call("hostgroup/$fullname/description/$desc", 'PUT');
        $this->dc->commit();
        return $json;
    }

    public function setThresholds($thresholds) {
        $res = array();

        foreach ($thresholds as $key => $value) {
            $fullname = $this->fullname();
            $value = urlencode($value);
            $json = $this->dc->call("hostgroup/$fullname/$key/$value", 'PUT');

            $json['error_text_full'] = $key . ": " .$json['error_text'];
            $res[] = $json;
        }

        $this->dc->commit();

        return collect($res);
    }

    public function manageIP($ip = null, $action = "PUT", $commit = true) {
        if(is_null($ip) || empty($ip)) {
            return false;
        }

        $fullname = $this->fullname();
        $ip = urlencode($ip);
        $json = $this->dc->call("hostgroup/$fullname/networks/$ip", $action);
        if($commit) {
            $this->dc->commit();
        }
        return $json;
    }
}
