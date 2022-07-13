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
    // id	`type`	alias	plants_id	UUID	relay_pin	ipaddr
    public function up()
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('type', 50);
            $table->string('alias', 100);
            $table->foreignId('plants_id');
            $table->uuid('uuid');
            $table->foreignId('relay_pins_id');
            $table->ipAddress('ipaddr');
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
