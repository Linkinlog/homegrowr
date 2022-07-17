<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // id	sensor_id	value	status_id	TS	updated_at	created_at
        Schema::create('readings', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->foreignId('sensor_id')->nullable();
            $table->unsignedDecimal('value', 16, 4)->nullable();
            $table->foreignId('status_id')->nullable();
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
        Schema::dropIfExists('readings');
    }
}
