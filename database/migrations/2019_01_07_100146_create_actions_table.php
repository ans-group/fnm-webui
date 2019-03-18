<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('hostgroup_id')->nullable();
            $table->integer('ip_id')->nullable();
            $table->integer('dc_id')->nullable();
            $table->string('action');

            $table->string('uuid')->nulable();
            $table->string('ip')->nullable();

            $table->string('attack_severity')->nullable();
            $table->string('attack_direction')->nullable();
            $table->string('attack_type')->nullable();
            $table->string('attack_protocol')->nullable();
            $table->string('attack_detection_source')->nullable();

            $table->integer('attack_initial_power')->nullable();
            $table->integer('attack_peak_power')->nullable();
            $table->integer('attack_total_incoming_traffic')->nullable();
            $table->integer('attack_total_outgoing_traffic')->nullable();
            $table->integer('attack_total_incoming_pps')->nullable();
            $table->integer('attack_total_outgoing_pps')->nullable();
            $table->integer('attack_total_incoming_flows')->nullable();
            $table->integer('attack_total_outgoing_flows')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
}
