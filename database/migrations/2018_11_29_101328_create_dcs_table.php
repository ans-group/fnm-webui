<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDCsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->boolean('active');
            $table->string('name');
            $table->text('description')->nullable();

            $table->string('api_url');
            $table->string('api_username');
            $table->string('api_password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dc');
    }
}
