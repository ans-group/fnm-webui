<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actions extends Model
{
    protected $fillable = [
        'id','created_at','updated_at','hostgroup_id','ip_id','action','uuid','ip','attack_severity','attack_detection_threshold','attack_detection_threshold_direction','attack_detection_source','attack_total_incoming_traffic','attack_total_outgoing_traffic','attack_total_incoming_pps','attack_total_outgoing_pps','attack_total_incoming_flows','attack_total_outgoing_flows',
    ];

    public function range() {
        return $this->belongsTo('App\IP');
    }

    public function hostgroup() {
        return $this->belongsTo('App\HostGroup');
    }

    public function dc() {
        return $this->hostgroup->dc();
    }

    public function rawPretty() {
        $decode = json_decode($this->raw);
        $pretty = json_encode($decode, JSON_PRETTY_PRINT);
        return $pretty;
    }

}
