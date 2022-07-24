<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\Reading;
use App\Models\Sensor;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Reading::factory()
            ->count(50)
            ->forSensor()
            ->hasPlants(2)
            ->create();
    }
}
