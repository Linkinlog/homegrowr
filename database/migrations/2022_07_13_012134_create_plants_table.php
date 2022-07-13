<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // id	name	plant_date	location	harvest_date
        Schema::create('plants', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name', 50);
            $table->date('plant_date');
            $table->string('location', 100);
            $table->date('harvest_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plants');
    }
}
