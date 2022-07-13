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
        // id	sensors_id	value	status_id	TS	updated_at	created_at
        Schema::create('readings', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->foreignId('sensors_id');
            $table->unsignedDecimal('value', 16, 4);
            $table->foreignId('status_id');
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
