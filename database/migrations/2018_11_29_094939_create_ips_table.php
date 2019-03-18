<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIPsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->unsignedInteger('hostgroup_id');

            $table->string('range'); // CIDR notation for range: 1.2.3.0/24
            $table->text('description')->nullable();
            $table->enum('version', ['4', '6'])->default('4');
            $table->unsignedInteger('start_ip'); // This will be stored with INET_ATON
            $table->unsignedInteger('end_ip'); // This will be stored with INET_ATON
            $table->unsignedInteger('cidr'); // Number of CIDR bits
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ip');
    }
}
