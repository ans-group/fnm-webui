<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('attack_detection_threshold')->nullable();
            $table->string('attack_detection_threshold_direction')->nullable();
            $table->dropColumn(['attack_type', 'attack_direction', 'attack_protocol', 'attack_initial_power', 'attack_peak_power']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actions', function (Blueprint $table) {
                $table->dropColumn(['attack_detection_threshold', 'attack_detection_threshold_direction']);
                $table->string('attack_direction')->nullable();
                $table->string('attack_type')->nullable();
                $table->string('attack_protocol')->nullable();
        });
    }
};
