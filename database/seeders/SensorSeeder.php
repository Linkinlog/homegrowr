<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\Relay;
use App\Models\Sensor;
use App\Models\Type;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sensor::factory()
            ->count(5)
            ->hasReadings(50)
            ->hasLocations(2)
            ->hasPlants(2)
            ->hasRelays(2)
            ->hasTypes(2)
            ->create();
    }
}
