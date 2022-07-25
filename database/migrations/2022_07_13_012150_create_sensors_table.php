<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // id	`type`	alias	plant_id	UUID	relay_pin	ipaddr
    public function up()
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('alias', 100)->nullable();
            $table->uuid('uuid')->nullable();
            $table->foreignId('location_id')->nullable();
            $table->ipAddress('ipaddr')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensors');
    }
}
